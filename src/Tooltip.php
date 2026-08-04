<?php

declare(strict_types=1);

namespace Drupal\neo_tooltip;

use Drupal\Component\Render\MarkupInterface;
use Drupal\Core\Link;
use Drupal\Core\Template\Attribute;
use Drupal\neo_settings\SettingsTrait;

/**
 * A tooltip.
 */
class Tooltip {

  use SettingsTrait;

  /**
   * The settings ID.
   *
   * @var string
   */
  protected string $settingsId = 'neo_tooltip.settings';

  /**
   * The content of the tooltip.
   *
   * @var string|array
   */
  protected mixed $content = '';

  /**
   * Whether the content is a template.
   *
   * @var bool
   */
  protected bool $contentAsTemplate = FALSE;

  /**
   * The theme of the tooltip.
   *
   * @var string
   */
  protected string $theme = '';

  /**
   * The placement of the tooltip.
   *
   * @var string
   */
  protected string $placement = '';

  /**
   * The animation of the tooltip.
   *
   * @var string
   */
  protected string $animation = '';

  /**
   * Whether the tooltip has an arrow.
   *
   * @var bool
   */
  protected bool $arrow = TRUE;

  /**
   * The delay of the tooltip.
   *
   * @var int|string
   */
  protected int|string $delay = 0;

  /**
   * The trigger of the tooltip.
   *
   * @var string
   */
  protected string $trigger = '';

  /**
   * The trigger to nearest focusable.
   *
   * @var string
   */
  protected bool $triggerToNearestFocusable = FALSE;

  /**
   * Constructs a new Tooltip.
   *
   * @param mixed $content
   *   The content of the tooltip.
   * @param array $options
   *   An array of options.
   */
  public function __construct($content = NULL, array $options = []) {
    if ($content) {
      $this->setContent($content);
    }
    foreach ($options as $key => $value) {
      $method = 'set' . ucfirst($key);
      if (method_exists($this, $method)) {
        $this->$method($value);
      }
    }
  }

  /**
   * Sets the content of the tooltip.
   *
   * @param mixed $content
   *   The content of the tooltip.
   *
   * @return $this
   */
  public function setContent(mixed $content):self {
    if (is_array($content) || $content instanceof MarkupInterface) {
      $this->contentAsTemplate = TRUE;
      // $content = (string) $content;
    }
    $this->content = $content;
    return $this;
  }

  /**
   * Returns the content of the tooltip.
   *
   * @return mixed
   *   The content of the tooltip.
   */
  public function getContent():mixed {
    return $this->content;
  }

  /**
   * Returns an array of tooltip placements.
   *
   * @return array
   *   An array of tooltip placements, where the keys represent the placement
   *   values and the values represent the human-readable labels.
   */
  public function getPlacements():array {
    return [
      'top' => 'Top',
      'top-start' => 'Top Start',
      'top-end' => 'Top End',
      'bottom' => 'Bottom',
      'bottom-start' => 'Bottom Start',
      'bottom-end' => 'Bottom End',
      'left' => 'Left',
      'left-start' => 'Left Start',
      'left-end' => 'Left End',
      'right' => 'Right',
      'right-start' => 'Right Start',
      'right-end' => 'Right End',
      'auto' => 'Auto',
      'auto-start' => 'Auto Start',
      'auto-end' => 'Auto End',
    ];
  }

  /**
   * Returns an array of available animations for the tooltip.
   *
   * @return array
   *   An array of animations, where the keys are animation codes and the values
   *   are animation names.
   */
  public function getAnimations():array {
    return [
      'false' => 'None',
      'shift-away' => 'Shift Away',
      'shift-toward' => 'Shift Toward',
      'fade' => 'Fade',
      'scale' => 'Scale',
      'perspective' => 'Perspective',
    ];
  }

  /**
   * Returns an array of available triggers for the tooltip.
   *
   * @return array
   *   An array of triggers, where the keys are trigger codes and the values are
   *   trigger names.
   */
  public function getTriggers():array {
    return [
      'mouseenter focus' => 'Mouse Enter',
      'click' => 'Click',
      'focusin' => 'Focus In',
      'mouseenter click' => 'Mouseenter Click',
      'manual' => 'Manual',
    ];
  }

  /**
   * Sets the theme of the tooltip.
   *
   * @var string
   *   The theme value.
   *
   * @return $this
   */
  public function setTheme(string $value):self {
    $this->theme = $value;
    return $this;
  }

  /**
   * Returns the theme of the tooltip.
   *
   * @return string
   *   The theme value.
   */
  public function getTheme():string {
    return $this->theme ?: 'neo';
  }

  /**
   * Returns the placement of the tooltip.
   *
   * @return string
   *   The placement value.
   */
  public function getPlacement():string {
    return $this->placement ?: $this->getSettings()->getValue('placement');
  }

  /**
   * Sets the placement of the tooltip.
   *
   * @param string $value
   *   The placement value.
   *
   * @return $this
   */
  public function setPlacement(string $value):self {
    if (isset($this->getPlacements()[$value])) {
      $this->placement = $value;
    }
    return $this;
  }

  /**
   * Sets the placement of the tooltip to top.
   *
   * @return $this
   */
  public function setPlacementToTop():self {
    $this->setPlacement('top');
    return $this;
  }

  /**
   * Sets the placement of the tooltip to top start.
   *
   * @return $this
   */
  public function setPlacementToTopStart():self {
    $this->setPlacement('top-start');
    return $this;
  }

  /**
   * Sets the placement of the tooltip to top end.
   *
   * @return $this
   */
  public function setPlacementToTopEnd():self {
    $this->setPlacement('top-end');
    return $this;
  }

  /**
   * Sets the placement of the tooltip to bottom.
   *
   * @return $this
   */
  public function setPlacementToBottom():self {
    $this->setPlacement('bottom');
    return $this;
  }

  /**
   * Sets the placement of the tooltip to bottom start.
   *
   * @return $this
   */
  public function setPlacementToBottomStart():self {
    $this->setPlacement('bottom-start');
    return $this;
  }

  /**
   * Sets the placement of the tooltip to bottom end.
   *
   * @return $this
   */
  public function setPlacementToBottomEnd():self {
    $this->setPlacement('bottom-end');
    return $this;
  }

  /**
   * Sets the placement of the tooltip to left.
   *
   * @return $this
   */
  public function setPlacementToLeft():self {
    $this->setPlacement('left');
    return $this;
  }

  /**
   * Sets the placement of the tooltip to left start.
   *
   * @return $this
   */
  public function setPlacementToLeftStart():self {
    $this->setPlacement('left-start');
    return $this;
  }

  /**
   * Sets the placement of the tooltip to left end.
   *
   * @return $this
   */
  public function setPlacementToLeftEnd():self {
    $this->setPlacement('left-end');
    return $this;
  }

  /**
   * Sets the placement of the tooltip to right.
   *
   * @return $this
   */
  public function setPlacementToRight():self {
    $this->setPlacement('right');
    return $this;
  }

  /**
   * Sets the placement of the tooltip to right start.
   *
   * @return $this
   */
  public function setPlacementToRightStart():self {
    $this->setPlacement('right-start');
    return $this;
  }

  /**
   * Sets the placement of the tooltip to right end.
   *
   * @return $this
   */
  public function setPlacementToRightEnd():self {
    $this->setPlacement('right-end');
    return $this;
  }

  /**
   * Sets the placement of the tooltip to auto.
   *
   * @return $this
   */
  public function setPlacementToAuto():self {
    $this->setPlacement('auto');
    return $this;
  }

  /**
   * Sets the placement of the tooltip to auto start.
   *
   * @return $this
   */
  public function setPlacementToAutoStart():self {
    $this->setPlacement('auto-start');
    return $this;
  }

  /**
   * Sets the placement of the tooltip to auto end.
   *
   * @return $this
   */
  public function setPlacementToAutoEnd():self {
    $this->setPlacement('auto-end');
    return $this;
  }

  /**
   * Returns the animation of the tooltip.
   *
   * @return string
   *   The animation value.
   */
  public function getAnimation():string {
    return $this->animation ?: $this->getSettings()->getValue('animation');
  }

  /**
   * Sets the animation of the tooltip.
   *
   * @param string $value
   *   The animation name.
   *
   * @return $this
   */
  public function setAnimation(string $value):self {
    if (isset($this->getAnimations()[$value])) {
      $this->animation = $value;
    }
    return $this;
  }

  /**
   * Sets the animation of the tooltip to none.
   *
   * @return $this
   */
  public function setAnimationToNone():self {
    $this->setAnimation('false');
    return $this;
  }

  /**
   * Sets the animation of the tooltip to fade.
   *
   * @return $this
   */
  public function setAnimationToFade():self {
    $this->setAnimation('fade');
    return $this;
  }

  /**
   * Sets the animation of the tooltip to shift away.
   *
   * @return $this
   */
  public function setAnimationToShiftAway():self {
    $this->setAnimation('shift-away');
    return $this;
  }

  /**
   * Sets the animation of the tooltip to toward away.
   *
   * @return $this
   */
  public function setAnimationToShiftToward():self {
    $this->setAnimation('shift-toward');
    return $this;
  }

  /**
   * Sets the animation of the tooltip to scale.
   *
   * @return $this
   */
  public function setAnimationToScale():self {
    $this->setAnimation('scale');
    return $this;
  }

  /**
   * Sets the animation of the tooltip to perspective.
   *
   * @return $this
   */
  public function setAnimationToPerspective():self {
    $this->setAnimation('perspective');
    return $this;
  }

  /**
   * Returns whether the tooltip has an arrow.
   *
   * @return bool
   *   The arrow value.
   */
  public function getArrow():bool {
    return $this->arrow;
  }

  /**
   * Sets whether the tooltip has an arrow.
   *
   * @param bool $value
   *   The arrow value.
   *
   * @return $this
   */
  public function setArrow(bool $value):self {
    $this->arrow = $value;
    return $this;
  }

  /**
   * Returns the tooltip delay.
   *
   * @return int|string
   *   The delay value.
   */
  public function getDelay():int|string {
    return $this->delay;
  }

  /**
   * Sets the delay.
   *
   * @param int|string $value
   *   The delay value. Example: '500' or '[500, 100]'.
   *
   * @return $this
   */
  public function setDelay(int|string $value):self {
    $this->delay = $value;
    return $this;
  }

  /**
   * Returns the trigger of the tooltip.
   *
   * @return string
   *   The trigger value.
   */
  public function getTrigger():string {
    return $this->trigger ?: $this->getSettings()->getValue('trigger');
  }

  /**
   * Sets the trigger of the tooltip.
   *
   * @param string $value
   *   The trigger value. Can be one of 'mouseenter focus', 'click', 'focusin',
   *   'mouseenter click', or 'manual'.
   *
   * @return $this
   */
  public function setTrigger(string $value):self {
    if (isset($this->getTriggers()[$value])) {
      $this->trigger = $value;
    }
    return $this;
  }

  /**
   * Sets the trigger to the nearest focusable element.
   *
   * @return $this
   */
  public function setTriggerToNearestFocusableElement():self {
    $this->triggerToNearestFocusable = TRUE;
    return $this;
  }

  /**
   * Returns the attributes for the tooltip.
   *
   * @return \Drupal\Core\Template\Attribute
   *   The attributes.
   */
  public function getAttributes():Attribute {
    $attributes = [];
    $attributes['class'][] = 'use-neo-tooltip';
    $theme = $this->getTheme();
    if ($theme) {
      $attributes['data-tippy-theme'] = $theme;
    }
    $placement = $this->getPlacement();
    if ($placement && $placement !== $this->getSettings()->getValue('placement')) {
      $attributes['data-tippy-placement'] = $placement;
    }
    $animation = $this->getAnimation();
    if ($animation && $animation !== $this->getSettings()->getValue('animation')) {
      $attributes['data-tippy-animation'] = $animation;
    }
    $trigger = $this->getTrigger();
    if ($trigger && $trigger !== $this->getSettings()->getValue('trigger')) {
      $attributes['data-tippy-trigger'] = $trigger;
    }
    $delay = $this->getDelay();
    if ($delay) {
      $attributes['data-tippy-delay'] = $delay;
    }
    if ($this->getArrow() === FALSE) {
      $attributes['data-tippy-arrow'] = 'false';
    }
    if ($this->triggerToNearestFocusable) {
      $attributes['data-tippy-trigger-nearest'] = 'true';
    }
    if ($this->content && !$this->contentAsTemplate) {
      $attributes['data-tippy-content'] = $this->content;
    }
    return new Attribute($attributes);
  }

  /**
   * Returns the attachments for the tooltip.
   *
   * @return array
   *   The attachments.
   */
  public function getAttachments():array {
    $attachments = [];
    $attachments['library'][] = 'neo_tooltip/tooltip';
    return $attachments;
  }

  /**
   * Prepare the build.
   *
   * @param array $build
   *   The renderable array.
   */
  protected function buildTrigger(array $build) {
    if (empty($build['#type']) || in_array($build['#type'], [
      'markup',
      'plain_text',
      'neo_icon',
    ])) {
      $inner = $build;
      // The wrapper is the trigger from here on: the tooltip attributes are
      // merged into it, not into what it wraps. Marking the wrapped element as
      // built keeps the process callback from treating it as a fresh trigger
      // when the form builder walks into it — it still carries the #tooltip
      // that got it here, so it would be wrapped again, and again.
      $inner['#neo_tooltip_built'] = TRUE;
      $build = static::inheritStructure([
        '#type' => 'html_tag',
        '#tag' => 'a',
        '#attributes' => [
          'class' => [
            'cursor-help',
            'text-inherit',
            'hover:text-inherit',
          ],
          'href' => '',
          'onclick' => 'return false;',
        ],
        'value' => $inner,
      ], $inner);
      if ($this->triggerToNearestFocusable) {
        $build['#tag'] = 'span';
        unset($build['#attributes']);
      }
    }
    elseif (in_array($build['#type'], [
      'submit',
      'button',
    ]) && !empty($build['#disabled'])) {
      $inner = $build;
      $inner['#neo_tooltip_built'] = TRUE;
      $build = static::inheritStructure([
        '#type' => 'html_tag',
        '#tag' => 'div',
        '#attributes' => [],
        'value' => $inner,
      ], $inner);
    }
    $build['#neo_tooltip_built'] = TRUE;
    return $build;
  }

  /**
   * Carries an element's structural properties over to a wrapper around it.
   *
   * Wrapping happens from a #process callback, which runs inside
   * FormBuilder::doBuildForm() — after the builder has already stamped the
   * element with the properties it uses to walk the tree. Handing back a fresh
   * wrapper without them makes the builder emit "Undefined array key
   * #array_parents" for every descendant of the wrapper, on every build, each
   * with a full backtrace. #weight goes along too, or the wrapped element
   * silently loses its place in the parent's ordering.
   *
   * @param array $wrapper
   *   The wrapper element.
   * @param array $original
   *   The element being wrapped.
   *
   * @return array
   *   The wrapper, carrying the original's structural properties.
   */
  protected static function inheritStructure(array $wrapper, array $original): array {
    foreach (['#array_parents', '#parents', '#weight'] as $property) {
      if (isset($original[$property])) {
        $wrapper[$property] = $original[$property];
      }
    }
    return $wrapper;
  }

  /**
   * Apply the tooltip to an attribute.
   *
   * @param \Drupal\Core\Template\Attribute $attribute
   *   The attribute.
   */
  public function applyToAttribute(Attribute $attribute):void {
    $attribute->merge($this->getAttributes());
  }

  /**
   * Apply the tooltip to a link.
   *
   * @param \Drupal\Core\Link $link
   *   The link.
   */
  public function applyToLink(Link $link):void {
    $url = $link->getUrl();
    $attributes = $url->getOptions()['attributes'] ?? [];
    $attribute = new Attribute($attributes);
    $attribute->merge($this->getAttributes());
    $attribute->removeAttribute('title');
    if ($this->content && $this->contentAsTemplate) {
      $id = 'tooltip-' . uniqid();
      $attribute->setAttribute('data-tippy-template', $id);
      $build['#attached']['drupalSettings']['neoTooltipTemplates'][$id] = is_array($this->content) ? \Drupal::service('renderer')->render($this->content) : $this->content;
    }
    $attributes = $attribute->toArray();
    $url->setOption('attributes', $attributes);
  }

  /**
   * Create from markup.
   */
  public static function createFromMarkup(MarkupInterface $markup, $content = NULL, array $options = []): array {
    $trigger = [
      '#markup' => $markup,
    ];
    $tooltip = new self($content, $options);
    $tooltip->applyTo($trigger);
    return $trigger;
  }

  /**
   * Apply the tooltip to a renderable array.
   *
   * @param array $build
   *   The renderable array.
   * @param string $attributeProperty
   *   (optional) The property of the trigger element that will contain the
   *   tooltip attributes. Defaults to '#attributes'.
   */
  public function applyTo(array &$build, $attributeProperty = '#attributes'):void {
    $build = $this->buildTrigger($build);
    $build[$attributeProperty] = $build[$attributeProperty] ?? [];
    $attribute = new Attribute($build[$attributeProperty]);
    $attribute->merge($this->getAttributes());
    if ($this->content && $this->contentAsTemplate) {
      $id = 'tooltip-' . uniqid();
      $attribute->setAttribute('data-tippy-template', $id);
      $build['#attached']['drupalSettings']['neoTooltipTemplates'][$id] = is_array($this->content) ? \Drupal::service('renderer')->render($this->content) : $this->content;
    }
    $build[$attributeProperty] = $attribute->toArray();
    foreach ($this->getAttachments() as $attachmentType => $attachments) {
      foreach ($attachments as $attachment) {
        $build['#attached'][$attachmentType][] = $attachment;
      }
    }
  }

  /**
   * Apply the tooltip to a string.
   *
   * @param string|MarkupInterface $string
   *   The string.
   *
   * @return array
   *   The renderable array.
   */
  public function buildFromString(string|MarkupInterface $string) {
    $build = [
      '#type' => 'markup',
      '#markup' => $string,
    ];
    $this->applyTo($build);
    return $build;
  }

}
