<?php

declare(strict_types=1);

namespace Drupal\Tests\neo_tooltip\Kernel;

use Drupal\Core\Form\FormState;
use Drupal\KernelTests\KernelTestBase;
use Drupal\neo_tooltip\ElementProcess;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;

/**
 * Tests what ElementProcess does with a form element's description.
 *
 * Two things are being pinned down here.
 *
 * The first is an accessibility fix. The callback used to null `#description`
 * once it had copied the text into a tooltip, but FormBuilder::doBuildForm()
 * stamps `aria-describedby` on the control from that same property *before* it
 * runs the process callbacks — so the attribute survived and the element it
 * pointed at did not. Keeping the description and hiding it visually is what
 * closes that. It leaves no visible trace in the page, so it needs asserting
 * directly — which is what the `tooltip` case below does.
 *
 * The second is the mode switch. `auto` is the only branch that reads the
 * description rather than just routing on the setting, so its boundary and its
 * measured-on-plain-text rule are covered directly.
 */
#[Group('neo_tooltip')]
class DescriptionModeTest extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'system',
    'user',
    // neo's linkit_resolver service references both of these, and the container
    // checks its arguments at compile time whether or not a test ever reaches
    // that code — so they are here to get a container, not because anything
    // below touches an alias or a link.
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
   * Puts the module into one of the three description modes.
   *
   * Call this before the first process() in a test and not again: the settings
   * repository memoises the active settings the first time it is asked, and
   * exposes no way to drop them, so a second mode inside one test would be
   * read as the first. Covering several modes means several tests — hence the
   * data provider below rather than a loop.
   */
  protected function setMode(string $mode, ?int $maxLength = NULL): void {
    $config = $this->config('neo_tooltip.settings')->set('description_mode', $mode);
    if ($maxLength !== NULL) {
      $config->set('description_max_length', $maxLength);
    }
    $config->save();
  }

  /**
   * Runs the process callback over an element and hands back the result.
   */
  protected function process(array $element): array {
    $element += ['#type' => 'textfield', '#id' => 'edit-demo'];
    $formState = new FormState();
    $complete = [];
    return ElementProcess::processInput($element, $formState, $complete);
  }

  /**
   * Whether the element came back carrying a tooltip.
   */
  protected function hasTooltip(array $element): bool {
    return in_array('use-neo-tooltip', $element['#attributes']['class'] ?? [], TRUE)
      || in_array('use-neo-tooltip', $element['#wrapper_attributes']['class'] ?? [], TRUE);
  }

  /**
   * A converted description is hidden, never dropped.
   */
  public function testTooltipModeKeepsTheDescriptionForScreenReaders(): void {
    $this->setMode(ElementProcess::DESCRIPTION_TOOLTIP);
    $element = $this->process(['#description' => 'Shown when hovering over the menu link.']);

    $this->assertTrue($this->hasTooltip($element));
    // The property the aria-describedby target is rendered from. Nulling it is
    // what broke the reference, so its survival is the assertion that matters.
    $this->assertSame('Shown when hovering over the menu link.', $element['#description']);
    $this->assertSame('invisible', $element['#description_display']);
  }

  /**
   * Inline mode leaves the description exactly where Drupal put it.
   */
  public function testInlineModeLeavesTheDescriptionAlone(): void {
    $this->setMode(ElementProcess::DESCRIPTION_INLINE);
    $element = $this->process(['#description' => 'Shown when hovering over the menu link.']);

    $this->assertFalse($this->hasTooltip($element));
    $this->assertSame('Shown when hovering over the menu link.', $element['#description']);
    $this->assertArrayNotHasKey('#description_display', $element);
  }

  /**
   * Auto mode converts at the cutoff and not past it.
   */
  public function testAutoModeSplitsOnLength(): void {
    $this->setMode(ElementProcess::DESCRIPTION_AUTO, 20);

    // Exactly the cutoff converts — the comparison is inclusive.
    $atLimit = $this->process(['#description' => str_repeat('a', 20)]);
    $this->assertTrue($this->hasTooltip($atLimit));

    $overLimit = $this->process(['#description' => str_repeat('a', 21)]);
    $this->assertFalse($this->hasTooltip($overLimit));
    $this->assertArrayNotHasKey('#description_display', $overLimit);
  }

  /**
   * Auto mode measures the text a reader sees, not the markup carrying it.
   */
  public function testAutoModeIgnoresMarkupWhenMeasuring(): void {
    $this->setMode(ElementProcess::DESCRIPTION_AUTO, 20);

    // Eleven characters of text inside far more than twenty of markup. Counting
    // the markup would push a short hint over the line and leave it inline.
    $element = $this->process([
      '#description' => '<a href="https://example.com/a/fairly/long/path">Learn more</a>',
    ]);
    $this->assertTrue($this->hasTooltip($element));
  }

  /**
   * Every mode this module has.
   */
  public static function modeProvider(): array {
    return [
      'tooltip' => [ElementProcess::DESCRIPTION_TOOLTIP],
      'auto' => [ElementProcess::DESCRIPTION_AUTO],
      'inline' => [ElementProcess::DESCRIPTION_INLINE],
    ];
  }

  /**
   * An explicit `#tooltip => FALSE` opts out, whatever the mode.
   */
  #[DataProvider('modeProvider')]
  public function testTooltipFalseOptsOut(string $mode): void {
    $this->setMode($mode);
    $element = $this->process([
      '#description' => 'Short hint.',
      '#tooltip' => FALSE,
    ]);

    $this->assertFalse($this->hasTooltip($element));
    $this->assertArrayNotHasKey('#description_display', $element);
  }

  /**
   * A deliberate `#tooltip` string is a tooltip in every mode.
   *
   * The setting governs help text that was never written with a tooltip in
   * mind. A developer who asked for one has already made the call.
   */
  public function testExplicitTooltipStringIgnoresTheMode(): void {
    $this->setMode(ElementProcess::DESCRIPTION_INLINE);
    $element = $this->process([
      '#description' => 'A description that stays put.',
      '#tooltip' => 'A hint that does not.',
    ]);

    $this->assertTrue($this->hasTooltip($element));
    // The description was not consumed to build it, so it still renders.
    $this->assertSame('A description that stays put.', $element['#description']);
    $this->assertArrayNotHasKey('#description_display', $element);
  }

}
