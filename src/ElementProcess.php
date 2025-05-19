<?php

namespace Drupal\neo_tooltip;

use Drupal\Component\Render\MarkupInterface;
use Drupal\Core\Form\FormStateInterface;

/**
 * Implements trusted prerender callbacks for the Claro theme.
 *
 * @internal
 */
class ElementProcess {

  /**
   * Apply tooltip to form elements.
   */
  public static function processInput(&$element, FormStateInterface $form_state, &$complete_form) {
    // Apply tooltip to all elements with a description.
    if (!empty($element['#neo_tooltip_built'])) {
      return $element;
    }

    // If tooltip is set and is FALSE, do not apply.
    if (isset($element['#tooltip']) && $element['#tooltip'] === FALSE) {
      return $element;
    }

    $tooltip = NULL;
    $attributesProperty = 'attributes';
    $options = [
      'placement' => 'bottom-start',
      'delay' => '[300,100]',
      'triggerToNearestFocusableElement' => TRUE,
    ];
    if (isset($element['#type']) && in_array($element['#type'], ['button', 'submit'])) {
      $options['placement'] = 'bottom';
    }

    // If we have a tooltip set as a string.
    if (isset($element['#tooltip']) && (is_string($element['#tooltip']) || $element['#tooltip'] instanceof MarkupInterface)) {
      $tooltip = new Tooltip($element['#tooltip'], $options);
    }
    // If we have a description set as a string.
    elseif (!empty($element['#description']) && (is_string($element['#description']) || $element['#description'] instanceof MarkupInterface)) {
      $options = isset($element['#tooltip']) && is_array($element['#tooltip']) ? $element['#tooltip'] : [];
      $options = [
        'placement' => 'bottom-start',
        'delay' => '[300,100]',
        'triggerToNearestFocusableElement' => TRUE,
      ] + $options;
      $tooltip = new Tooltip($element['#description'], $options);
      $element['#description'] = NULL;
    }

    if ($tooltip) {
      // Checkboxes and radios need to use the wrapper attributes.
      if (isset($element['#type']) && in_array($element['#type'], ['checkboxes', 'radios'])) {
        $attributesProperty = 'wrapper_attributes';
      }
      $tooltip->applyTo($element, '#' . $attributesProperty);
    }
    return $element;
  }

}
