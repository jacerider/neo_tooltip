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
   * Descriptions always become a tooltip.
   */
  const DESCRIPTION_TOOLTIP = 'tooltip';

  /**
   * Short descriptions become a tooltip; long ones render inline.
   */
  const DESCRIPTION_AUTO = 'auto';

  /**
   * Descriptions are left alone and render the way Drupal renders them.
   */
  const DESCRIPTION_INLINE = 'inline';

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
    // No placement here. It used to be pinned to `bottom-start`, which on a
    // stacked form drops the tooltip over the field below the one being read.
    // Leaving it out lets Tooltip::getPlacement() fall through to the site's
    // configured default, which is the setting's whole purpose.
    $options = [
      'delay' => '[300,100]',
      'triggerToNearestFocusableElement' => TRUE,
    ];
    // A button is the exception worth keeping: it sits in an actions row rather
    // than a column, so there is nothing underneath for the tooltip to cover.
    if (isset($element['#type']) && in_array($element['#type'], ['button', 'submit'])) {
      $options['placement'] = 'bottom';
    }

    // If we have a tooltip set as a string.
    if (isset($element['#tooltip']) && (is_string($element['#tooltip']) || $element['#tooltip'] instanceof MarkupInterface)) {
      $tooltip = new Tooltip($element['#tooltip'], $options);
      // A deliberate tooltip needs a visible trigger as much as a converted
      // description does — more so, since there is no description rendered
      // underneath to hint that the field has anything to say.
      $element['#neo_tooltip_help'] = static::settingValue('description_icon', TRUE) ? TRUE : FALSE;
    }
    // If we have a description set as a string.
    elseif (!empty($element['#description']) && (is_string($element['#description']) || $element['#description'] instanceof MarkupInterface)) {
      if (!static::descriptionBecomesTooltip($element['#description'])) {
        return $element;
      }
      $options = (isset($element['#tooltip']) && is_array($element['#tooltip']) ? $element['#tooltip'] : []) + $options;
      $options['describedElsewhere'] = TRUE;
      $tooltip = new Tooltip($element['#description'], $options);
      // Hidden, not removed. FormBuilder::doBuildForm() stamps
      // `aria-describedby` on the control from `#description` *before* it runs
      // the process callbacks that land here, so nulling the description left
      // that attribute pointing at an element that then never rendered — a
      // dangling reference, and no announced description at all. Tippy does not
      // stand in for it either: a description carrying markup is passed as a
      // template, which makes the instance interactive, and an interactive
      // tippy uses `aria-expanded` rather than `aria-describedby`.
      //
      // `invisible` keeps core's own description markup, id and all, and
      // FormPreprocess::preprocessFormElement() gives it `visually-hidden`. The
      // sighted reader sees the tooltip; the screen reader gets the text.
      $element['#description_display'] = 'invisible';
      // Flag for neo_tooltip_preprocess_form_element(), which turns it into the
      // help icon beside the label. Set here rather than there because this is
      // the only place that knows a description *became* a tooltip — by
      // preprocess time an untouched description and a converted one differ
      // only by `#description_display`, which a caller may also have set.
      $element['#neo_tooltip_help'] = static::settingValue('description_icon', TRUE) ? TRUE : FALSE;
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

  /**
   * Whether a description should be moved into a tooltip.
   *
   * Only descriptions are asked. An explicit `#tooltip` is a developer saying
   * "this is a hint", and stays a tooltip in every mode — the setting governs
   * what happens to help text that was never written with a tooltip in mind.
   *
   * @param string|\Drupal\Component\Render\MarkupInterface $description
   *   The element's description.
   *
   * @return bool
   *   TRUE to convert, FALSE to leave the description where Drupal put it.
   */
  protected static function descriptionBecomesTooltip(string|MarkupInterface $description): bool {
    $mode = static::settingValue('description_mode', static::DESCRIPTION_TOOLTIP);
    if ($mode === static::DESCRIPTION_INLINE) {
      return FALSE;
    }
    if ($mode !== static::DESCRIPTION_AUTO) {
      return TRUE;
    }
    $max = (int) static::settingValue('description_max_length', 70);
    if ($max <= 0) {
      return TRUE;
    }
    // Measured on the text a reader sees, not on the markup carrying it: a
    // one-line hint wrapped in a link would otherwise count as long and stay
    // inline, which is the opposite of what the length is being asked about.
    $text = strip_tags((string) $description);
    $text = trim(html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
    return mb_strlen($text) <= $max;
  }

  /**
   * Reads a tooltip setting.
   *
   * Static because the process callback is, so there is no service to inject.
   * The repository caches, so this is not a config read per element.
   *
   * @param string $key
   *   The setting key.
   * @param mixed $fallback
   *   What to return before the module's config is in place — during install,
   *   and in a kernel test that has not imported it.
   *
   * @return mixed
   *   The stored value, or $fallback.
   */
  protected static function settingValue(string $key, mixed $fallback): mixed {
    if (!\Drupal::hasService('neo_tooltip.settings')) {
      return $fallback;
    }
    $value = \Drupal::service('neo_tooltip.settings')->getActive()->getValue($key);
    return $value ?? $fallback;
  }

}
