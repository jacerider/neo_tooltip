<?php

declare(strict_types=1);

namespace Drupal\Tests\neo_tooltip\Kernel;

use Drupal\Core\Form\FormPreprocess;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\Template\Attribute;
use Drupal\KernelTests\KernelTestBase;
use PHPUnit\Framework\Attributes\Group;

/**
 * Tests a group's own description becoming its tooltip.
 *
 * Core hands the two group templates their description in different shapes: a
 * fieldset gets an array carrying an attributes object, a details gets
 * `#description` exactly as it was set. That is usually a TranslatableMarkup —
 * a translated description is the ordinary case, not the odd one — and
 * probing it as though it were the fieldset's array was a fatal error that took
 * down any form with a described details on it, such as a node form carrying
 * metatags.
 *
 * The variables are built by core's own preprocess rather than by hand, so the
 * shape under test is the one a real page hands over.
 */
#[Group('neo_tooltip')]
class GroupDescriptionTest extends KernelTestBase {

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
   * A details with a translated description converts it without failing.
   */
  public function testDetailsWithMarkupDescription(): void {
    $variables = ['element' => [
      '#title' => 'Basic tags',
      '#description' => new TranslatableMarkup('Simple meta tags.'),
      '#attributes' => [],
      '#summary_attributes' => [],
    ]];
    $this->container->get(FormPreprocess::class)->preprocessDetails($variables);
    neo_tooltip_preprocess_details($variables);

    $this->assertInstanceOf(Attribute::class, $variables['neo_tooltip_help']['attributes']);
    $this->assertSame('invisible', $variables['description_display']);
  }

  /**
   * A fieldset with a translated description hides it in place.
   */
  public function testFieldsetWithMarkupDescription(): void {
    $variables = ['element' => [
      '#title' => 'Basic tags',
      '#description' => new TranslatableMarkup('Simple meta tags.'),
      '#description_display' => 'after',
      '#attributes' => ['id' => 'edit-basic'],
      '#children' => '',
    ]];
    $this->container->get(FormPreprocess::class)->preprocessFieldset($variables);
    neo_tooltip_preprocess_fieldset($variables);

    $this->assertInstanceOf(Attribute::class, $variables['neo_tooltip_help']['attributes']);
    $this->assertTrue($variables['description']['attributes']->hasClass('visually-hidden'));
  }

}
