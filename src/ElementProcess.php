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
    if (!empty($element['#description']) && (is_string($element['#description']) || $element['#description'] instanceof MarkupInterface)) {
      $options = isset($element['#tooltip']) && is_array($element['#tooltip']) ? $element['#tooltip'] : [];
      $options += [
        'placement' => 'bottom-start',
        'delay' => '[300,100]',
        'triggerToNearestFocusableElement' => TRUE,
      ];
      if (is_string($element['#description'])) {
        $element['#description'] = html_entity_decode($element['#description']);
      }
      $tooltip = new Tooltip($element['#description'], $options);
      $tooltip->applyTo($element);
      $element['#description'] = NULL;
    }
    return $element;
  }

}
