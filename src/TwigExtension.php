<?php

namespace Drupal\neo_tooltip;

use Drupal\Core\Template\Attribute;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

/**
 * Defines Twig extensions.
 */
class TwigExtension extends AbstractExtension {

  /**
   * Unique identifier for the tooltip.
   *
   * @var string
   */
  protected static string $id;

  /**
   * {@inheritdoc}
   */
  public function getFunctions() {
    return [
      new TwigFunction('neo_tooltip', [$this, 'renderTooltip']),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFilters():array {
    return [
      new TwigFilter('neo_tooltip_trigger', [$this, 'prepareTrigger']),
      new TwigFilter('neo_tooltip_content', [$this, 'prepareContent']),
    ];
  }

  /**
   * Render the tooltip.
   *
   * @param mixed $build
   *   The render array to which the tooltip should be applied.
   * @param mixed $content
   *   The content to be displayed in the tooltip.
   * @param array $options
   *   An array of options to be passed to the tooltip.
   *
   * @return mixed[]
   *   A render array.
   */
  public static function renderTooltip(mixed $build, mixed $content, array $options = []) {
    $tooltip = new Tooltip($content, $options);
    $tooltip->applyTo($build);
    return $build;
  }

  /**
   * Add classes to a renderable array.
   */
  public function prepareTrigger(Attribute $attribute, array $options = []) {
    $tooltip = new Tooltip($options['content'] ?? '', $options);
    $tooltip->getAttributes();
    $tooltip->applyToAttribute($attribute);
    if (empty($options['content'])) {
      self::$id = 'tooltip-' . uniqid();
      $attribute->setAttribute('data-tippy-template', self::$id);
    }
    return $attribute;
  }

  /**
   * Prepare content for tooltip.
   */
  public function prepareContent(mixed $content) {
    $build = [];
    $tooltip = new Tooltip();
    if (!is_array($content)) {
      $build['#attached']['drupalSettings']['neoTooltipTemplates'][self::$id] = $content;
    }
    foreach ($tooltip->getAttachments() as $attachmentType => $attachments) {
      foreach ($attachments as $attachment) {
        $build['#attached'][$attachmentType][] = $attachment;
      }
    }
    return $build;
  }

}
