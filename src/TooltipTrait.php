<?php

namespace Drupal\neo_tooltip;

use Drupal\Component\Render\MarkupInterface;
use Drupal\Core\Url;

/**
 * Wrapper methods for \Drupal\neo_tooltip\Tooltip.
 */
trait TooltipTrait {

  /**
   * Generates a tooltip and applies it to the given trigger.
   *
   * @param array|string $triggerBuildOrString
   *   The render array or string that will trigger the tooltip.
   * @param array|string|Drupal\Component\Render\MarkupInterface $buildOrString
   *   The render array or string that will be used as the content of the
   *   tooltip.
   * @param array $options
   *   (optional) An associative array of additional options for the tooltip.
   * @param string $attributeProperty
   *   (optional) The property of the trigger element that will contain the
   *   tooltip attributes. Defaults to '#attributes'.
   *
   * @return array
   *   The modified trigger render array with the tooltip applied.
   */
  protected function tooltip(array|string $triggerBuildOrString, array|string|MarkupInterface $buildOrString, array $options = [], $attributeProperty = '#attributes'): array {
    $tooltip = new Tooltip($buildOrString, $options);
    $tooltip->applyTo($triggerBuildOrString, $attributeProperty);
    return $triggerBuildOrString;
  }

  /**
   * Generates a tooltip link.
   *
   * This method creates a link element that triggers a tooltip.
   *
   * @param string $triggerString
   *   The text to be displayed as the tooltip trigger.
   * @param array|string|Drupal\Component\Render\MarkupInterface $buildOrString
   *   The content to be displayed inside the tooltip. This can be either a
   *   render array or a plain string.
   * @param array $options
   *   (optional) An array of additional options for the tooltip.
   * @param string $attributeProperty
   *   (optional) The property of the trigger element that will contain the
   *   tooltip attributes. Defaults to '#attributes'.
   *
   * @return array
   *   A render array representing the tooltip link.
   */
  protected function tooltipAsLink($triggerString, array|string|MarkupInterface $buildOrString, array $options = [], $attributeProperty = '#attributes'): array {
    $trigger = [
      '#type' => 'link',
      '#title' => $triggerString,
      '#url' => Url::fromRoute('<none>'),
    ];
    return $this->tooltip($trigger, $buildOrString, $options, $attributeProperty);
  }

}
