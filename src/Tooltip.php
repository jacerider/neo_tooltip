<?php

declare(strict_types = 1);

namespace Drupal\neo_tooltip;

use Drupal\Component\Render\FormattableMarkup;
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
   * @var int
   */
  protected int $delay = 0;

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
   */
  public function __construct($content = NULL) {
    if ($content) {
      $this->setContent($content);
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
    if ($content instanceof FormattableMarkup) {
      $content = (string) $content;
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
   * @return int
   *   The delay value.
   */
  public function getDelay():int {
    return $this->delay;
  }

  /**
   * Sets the delay.
   *
   * @param int $value
   *   The delay value.
   *
   * @return $this
   */
  public function setDelay(int $value):self {
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
    if ($this->content && is_string($this->content)) {
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
    ])) {
      $build = [
        '#type' => 'html_tag',
        '#tag' => 'a',
        '#attributes' => [
          'href' => '',
          'onclick' => 'return false;',
        ],
        'value' => $build,
      ];
      if ($this->triggerToNearestFocusable) {
        $build['#tag'] = 'span';
        unset($build['#attributes']);
      }
    }
    return $build;
  }

  /**
   * Build the content.
   *
   * @param mixed $content
   *   The tooltip content.
   *
   * @return array
   *   The renderable array.
   */
  public function buildTemplate(mixed $content):array {
    return [
      '#type' => 'html_tag',
      '#tag' => 'template',
      '#attributes' => [
        'class' => [
          'neo-tooltip-template',
        ],
      ],
      'template' => $content,
    ];
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
    if ($this->content && is_array($this->content)) {
      $attribute->setAttribute('data-tippy-template', 'true');
      $link->setText([
        'trigger' => ['#markup' => $link->getText()],
        'template' => $this->buildTemplate($this->content),
      ]);
    }
    $attributes = $attribute->toArray();
    $url->setOption('attributes', $attributes);
  }

  /**
   * Apply the tooltip to a renderable array.
   *
   * @param array $build
   *   The renderable array.
   */
  public function applyTo(array &$build):void {
    $build = $this->buildTrigger($build);
    $build['#attributes'] = $build['#attributes'] ?? [];
    $attribute = new Attribute($build['#attributes']);
    $attribute->merge($this->getAttributes());
    $build['#attributes'] = $attribute->toArray();
    foreach ($this->getAttachments() as $attachmentType => $attachments) {
      foreach ($attachments as $attachment) {
        $build['#attached'][$attachmentType][] = $attachment;
      }
    }
    if ($this->content && is_array($this->content)) {
      $build = [
        'trigger' => $build,
        'template' => $this->buildTemplate($this->content),
      ];
    }
  }

  /**
   * Apply the tooltip to a string.
   *
   * @param string $string
   *   The string.
   *
   * @return array
   *   The renderable array.
   */
  public function buildFromString(string $string) {
    $build = [
      '#type' => 'markup',
      '#markup' => $string,
    ];
    $this->applyTo($build);
    return $build;
  }

}
