<?php

declare(strict_types=1);

namespace Drupal\Tests\neo_tooltip\Kernel;

use Drupal\Core\Form\FormState;
use Drupal\KernelTests\KernelTestBase;
use Drupal\neo_tooltip\ElementProcess;
use PHPUnit\Framework\Attributes\Group;

/**
 * Tests where the visible help trigger for a converted description is drawn.
 *
 * The trigger is a badge in the field's own label, which works for as long as
 * there is a label on screen to put it in. Two ordinary cases mean there is
 * not: a control given `#title_display: invisible`, whose label renders inside
 * a `visually-hidden` box that clips the badge to a 0x0 square; and a control
 * with no `#title` at all, which renders no label at all. Both still get the
 * tooltip, so the help exists with nothing on screen to say so — and the
 * clipped badge is the worse of the two, because js/tooltip.ts then anchors
 * the tooltip to a box that can neither be hovered nor pointed at.
 *
 * What names such a control is the legend of the group around it, so the badge
 * is handed up to that group. These tests pin the handover down: who keeps
 * their own badge, who gives it away, who receives it, and what happens when
 * there is no-one to give it to.
 */
#[Group('neo_tooltip')]
class HelpTriggerPlacementTest extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'system',
    'user',
    // Present to get a container, not because anything here touches an alias
    // or a link — see DescriptionModeTest for the full explanation.
    'path_alias',
    'linkit',
    'neo',
    'neo_settings',
    'neo_tooltip',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->installConfig(['neo_tooltip']);
  }

  /**
   * Processes a described control sitting inside a group.
   *
   * `#array_parents` is set by hand rather than by running the whole form
   * builder: it is the only thing the ancestor walk reads, and stating it here
   * keeps the shape of the tree the test is about visible in the test.
   *
   * @param array $child
   *   Overrides for the control.
   * @param array $group
   *   Overrides for the group around it.
   *
   * @return array
   *   The processed control and the form it sat in, keyed `element` and
   *   `complete`.
   */
  protected function processInGroup(array $child, array $group = []): array {
    $child += [
      '#type' => 'select',
      '#id' => 'edit-prop-widget',
      '#description' => 'How many cards sit on a desktop row.',
      '#array_parents' => ['values', 'prop', 'widget'],
    ];
    $group += [
      '#type' => 'fieldset',
      '#title' => 'Items per row',
    ];
    $complete = ['values' => ['prop' => $group + ['widget' => $child]]];
    $formState = new FormState();
    $element = ElementProcess::processInput($child, $formState, $complete);
    return ['element' => $element, 'complete' => $complete];
  }

  /**
   * A control with a visible label keeps its own badge.
   */
  public function testVisibleLabelKeepsItsOwnTrigger(): void {
    ['element' => $element, 'complete' => $complete] = $this->processInGroup([
      '#title' => 'Items per row',
    ]);

    $this->assertTrue($element['#neo_tooltip_help']);
    // The group is left alone, or the same sentence would get two triggers.
    $this->assertArrayNotHasKey('#neo_tooltip_help', $complete['values']['prop']);
    $this->assertArrayNotHasKey(
      'data-neo-tooltip-help-id',
      $element['#wrapper_attributes'],
    );
  }

  /**
   * A control with no title hands its trigger to the group that names it.
   */
  public function testTitlelessControlPromotesToTheGroup(): void {
    ['element' => $element, 'complete' => $complete] = $this->processInGroup([
      '#title' => '',
    ]);

    // Not drawn beside the control: there is no label to draw it in.
    $this->assertFalse($element['#neo_tooltip_help']);
    $this->assertSame(
      ['target' => 'edit-prop-widget'],
      $complete['values']['prop']['#neo_tooltip_help'],
    );
    // The id is what ties the badge in the legend back to the instance built
    // on the form item, which sits outside it.
    $this->assertSame(
      'edit-prop-widget',
      $element['#wrapper_attributes']['data-neo-tooltip-help-id'],
    );
  }

  /**
   * A hidden label is treated as no label, because its badge is clipped too.
   */
  public function testInvisibleTitlePromotesToTheGroup(): void {
    ['element' => $element, 'complete' => $complete] = $this->processInGroup([
      '#title' => 'Size',
      '#title_display' => 'invisible',
    ]);

    $this->assertFalse($element['#neo_tooltip_help']);
    $this->assertSame(
      ['target' => 'edit-prop-widget'],
      $complete['values']['prop']['#neo_tooltip_help'],
    );
  }

  /**
   * A details element names its group as well as a fieldset does.
   */
  public function testDetailsReceivesThePromotion(): void {
    ['complete' => $complete] = $this->processInGroup(
      ['#title' => ''],
      ['#type' => 'details'],
    );

    $this->assertSame(
      ['target' => 'edit-prop-widget'],
      $complete['values']['prop']['#neo_tooltip_help'],
    );
  }

  /**
   * A group whose own legend is hidden cannot host a trigger either.
   */
  public function testGroupWithNoVisibleLegendIsSkipped(): void {
    ['element' => $element, 'complete' => $complete] = $this->processInGroup(
      ['#title' => ''],
      ['#title_display' => 'invisible'],
    );

    // Nowhere to put it, so the badge is given up rather than drawn somewhere
    // a reader cannot see. The tooltip and the announced description remain.
    $this->assertFalse($element['#neo_tooltip_help']);
    $this->assertArrayNotHasKey('#neo_tooltip_help', $complete['values']['prop']);
  }

  /**
   * A group with a description of its own keeps its legend for that.
   *
   * One legend cannot hold two triggers — a reader has no way to tell which
   * question mark belongs to the group and which to a field inside it — and the
   * group's own help has the better claim, because the legend is its own name.
   */
  public function testGroupWithItsOwnDescriptionIsNotUsed(): void {
    ['element' => $element, 'complete' => $complete] = $this->processInGroup(
      ['#title' => ''],
      ['#description' => 'What this whole group is for.'],
    );

    $this->assertFalse($element['#neo_tooltip_help']);
    $this->assertArrayNotHasKey('#neo_tooltip_help', $complete['values']['prop']);
    // The control keeps its tooltip, so the help is still reachable by hovering
    // it; only the badge is given up.
    $this->assertContains(
      'use-neo-tooltip',
      $element['#wrapper_attributes']['class'] ?? [],
    );
  }

  /**
   * The walk stops at the nearest group rather than climbing past it.
   *
   * A grandparent's legend names a broader set of fields, so a trigger drawn
   * there would say nothing about the control that handed it up.
   */
  public function testWalkStopsAtTheNearestGroup(): void {
    $child = [
      '#type' => 'select',
      '#id' => 'edit-prop-widget',
      '#title' => '',
      '#description' => 'How many cards sit on a desktop row.',
      '#array_parents' => ['values', 'outer', 'inner', 'widget'],
    ];
    $complete = [
      'values' => [
        'outer' => [
          '#type' => 'fieldset',
          '#title' => 'Outer group',
          'inner' => [
            // Cannot host: its own legend is hidden.
            '#type' => 'fieldset',
            '#title' => 'Inner group',
            '#title_display' => 'invisible',
            'widget' => $child,
          ],
        ],
      ],
    ];
    $formState = new FormState();
    ElementProcess::processInput($child, $formState, $complete);

    $this->assertArrayNotHasKey('#neo_tooltip_help', $complete['values']['outer']);
    $this->assertArrayNotHasKey(
      '#neo_tooltip_help',
      $complete['values']['outer']['inner'],
    );
  }

  /**
   * A plain container is not a group, so the walk passes through it.
   *
   * The nearest ancestor is not always the one that names the control — field
   * widgets in particular nest a container or two between the two.
   */
  public function testWalkPassesThroughContainersToTheGroup(): void {
    $child = [
      '#type' => 'select',
      '#id' => 'edit-prop-widget',
      '#title' => '',
      '#description' => 'How many cards sit on a desktop row.',
      '#array_parents' => ['values', 'prop', 'widget', 'widget'],
    ];
    $complete = [
      'values' => [
        'prop' => [
          '#type' => 'fieldset',
          '#title' => 'Items per row',
          'widget' => [
            '#type' => 'container',
            'widget' => $child,
          ],
        ],
      ],
    ];
    $formState = new FormState();
    ElementProcess::processInput($child, $formState, $complete);

    $this->assertArrayNotHasKey(
      '#neo_tooltip_help',
      $complete['values']['prop']['widget'],
    );
    $this->assertSame(
      ['target' => 'edit-prop-widget'],
      $complete['values']['prop']['#neo_tooltip_help'],
    );
  }

  /**
   * One legend, one badge — the first control to arrive keeps it.
   *
   * A group holding several described controls would otherwise grow a row of
   * identical question marks, none of which says which field it belongs to.
   */
  public function testGroupTakesOnlyTheFirstTrigger(): void {
    $first = [
      '#type' => 'select',
      '#id' => 'edit-prop-first',
      '#title' => '',
      '#description' => 'The first description.',
      '#array_parents' => ['values', 'prop', 'first'],
    ];
    $second = [
      '#type' => 'select',
      '#id' => 'edit-prop-second',
      '#title' => '',
      '#description' => 'The second description.',
      '#array_parents' => ['values', 'prop', 'second'],
    ];
    $complete = [
      'values' => [
        'prop' => [
          '#type' => 'fieldset',
          '#title' => 'Items per row',
          'first' => $first,
          'second' => $second,
        ],
      ],
    ];
    $formState = new FormState();
    ElementProcess::processInput($first, $formState, $complete);
    $secondResult = ElementProcess::processInput($second, $formState, $complete);

    $this->assertSame(
      ['target' => 'edit-prop-first'],
      $complete['values']['prop']['#neo_tooltip_help'],
    );
    // The loser gets no badge anywhere, but keeps its tooltip and its
    // announced description.
    $this->assertFalse($secondResult['#neo_tooltip_help']);
    $this->assertArrayNotHasKey(
      'data-neo-tooltip-help-id',
      $secondResult['#wrapper_attributes'],
    );
    $this->assertSame('invisible', $secondResult['#description_display']);
  }

}
