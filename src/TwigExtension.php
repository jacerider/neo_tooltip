<?php

namespace Drupal\neo_tooltip;

use Drupal\Core\Template\Attribute;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * Defines Twig extensions.
 */
class TwigExtension extends AbstractExtension {

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
   * Add classes to a renderable array.
   */
  public function prepareTrigger(Attribute $attribute, array $options = []) {
    $tooltip = new Tooltip($config['content'] ?? '', $options);
    $tooltip->getAttributes();
    $tooltip->applyToAttribute($attribute);
    if (empty($config['content'])) {
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
