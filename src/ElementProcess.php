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

    // Do not apply to hidden or value elements.
    if (isset($element['#type']) && in_array($element['#type'], [
      'hidden',
      'value',
      'table',
    ])) {
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
      // Controls whose input is not the thing on screen anchor to their wrapper
      // instead. A select is replaced by a scripted control, an autocomplete
      // grows one beside it, and a checkbox or radio is drawn entirely by its
      // label — the input behind it is either a styled box or, in the button
      // styles, `sr-only` and 1px square. Anchoring a tooltip to that leaves it
      // pointing at a corner of the control rather than at the control.
      //
      // The singular types belong here as much as the plural ones: the group
      // path already ends up on a per-item `.form-type--checkbox` wrapper
      // (see addInstance() in js/tooltip.ts), so this is the same anchor a
      // checkbox gets inside `checkboxes`, not a new convention.
      if (isset($element['#type']) && in_array($element['#type'], [
        'checkbox',
        'checkboxes',
        'radio',
        'radios',
        'select',
        'entity_autocomplete',
      ])) {
        $attributesProperty = 'wrapper_attributes';
      }
      $tooltip->applyTo($element, '#' . $attributesProperty);
    }
    return $element;
  }

}
