<?php

namespace Drupal\neo_tooltip;

use Drupal\Core\Security\TrustedCallbackInterface;
use Drupal\neo_icon\IconElementInterface;

/**
 * Implements trusted prerender callbacks for the Claro theme.
 *
 * @internal
 */
class ElementPreRender implements TrustedCallbackInterface {

  /**
   * Prerender callback for table.
   */
  public static function link($element) {
    if (empty($element['#tooltip'])) {
      return $element;
    }
    if ($element['#title'] instanceof IconElementInterface) {
      // Make sure the icon is not shown as a tooltip.
      $element['#title']->asTooltip(FALSE);
      if ($element['#title']->isIconOnly()) {
        // Set the text to empty so the default link title does not show when
        // hovering over the link.
        $element['#title']->setText('');
      }
    }
    $tooltip = new Tooltip($element['#tooltip']);
    $tooltip->applyTo($element);
    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public static function trustedCallbacks() {
    return [
      'link',
    ];
  }

}
