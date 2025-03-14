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
    if (!empty($element['#description']) && (is_string($element['#description']) || $element['#description'] instanceof MarkupInterface)) {
      if (isset($element['#tooltip']) && $element['#tooltip'] === FALSE) {
        return $element;
      }
      $options = isset($element['#tooltip']) && is_array($element['#tooltip']) ? $element['#tooltip'] : [];
      $options += [
        'placement' => 'bottom-start',
        'delay' => '[300,100]',
        'triggerToNearestFocusableElement' => TRUE,
      ];
      $tooltip = new Tooltip($element['#description'], $options);
      $element['#description'] = NULL;
      $tooltip->applyTo($element);
    }
    return $element;
  }

}
