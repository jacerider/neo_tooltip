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
      new TwigFilter('neo_tooltip', [$this, 'prepareTooltip']),
      new TwigFilter('neo_tooltip_trigger', [$this, 'prepareTrigger']),
      new TwigFilter('neo_tooltip_content', [$this, 'prepareContent']),
    ];
  }

  /**
   * Add classes to a renderable array.
   */
  public function prepareTrigger(Attribute $attribute, array $config = []) {
    $tooltip = new Tooltip($config['content'] ?? '');
    if (!empty($config['theme'])) {
      $tooltip->setTheme($config['theme']);
    }
    if (!empty($config['placement'])) {
      $tooltip->setPlacement($config['placement']);
    }
    if (!empty($config['animation'])) {
      $tooltip->setAnimation($config['animation']);
    }
    if (!empty($config['trigger'])) {
      $tooltip->setTrigger($config['trigger']);
    }
    if (!empty($config['arrow'])) {
      $tooltip->setArrow($config['arrow']);
    }
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
    // if (!isset($content['#type'])) {
    //   $content['#type'] = 'html_tag';
    //   $content['#tag'] = 'div';
    // }
    foreach ($tooltip->getAttachments() as $attachmentType => $attachments) {
      foreach ($attachments as $attachment) {
        $content['#attached'][$attachmentType][] = $attachment;
      }
    }
    return $tooltip->buildTemplate($content);
  }

}
