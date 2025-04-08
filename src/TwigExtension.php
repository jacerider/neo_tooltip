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
      $attribute->setAttribute('data-tippy-template', 'true');
    }
    return $attribute;
  }

  /**
   * Prepare content for tooltip.
   */
  public function prepareContent(mixed $content) {
    $tooltip = new Tooltip();
    if (!is_array($content)) {
      $content = [
        '#type' => 'markup',
        '#markup' => $content,
      ];
    }
    foreach ($tooltip->getAttachments() as $attachmentType => $attachments) {
      foreach ($attachments as $attachment) {
        $content['#attached'][$attachmentType][] = $attachment;
      }
    }
    return $tooltip->buildTemplate($content);
  }

}
