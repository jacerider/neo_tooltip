<?php

namespace Drupal\neo_tooltip\Settings;

use Drupal\Core\Form\FormStateInterface;
use Drupal\neo_settings\Plugin\SettingsBase;
use Drupal\neo_tooltip\ElementProcess;
use Drupal\neo_tooltip\Tooltip;

/**
 * Module settings.
 *
 * @Settings(
 *   id = "neo_tooltip",
 *   label = @Translation("Tooltip"),
 *   config_name = "neo_tooltip.settings",
 *   menu_title = @Translation("Tooltip"),
 *   route = "/admin/config/neo/neo-tooltip",
 *   admin_permission = "administer neo_tooltip",
 *   variation_allow = false,
 *   variation_conditions = false,
 *   variation_ordering = false,
 * )
 */
class TooltipSettings extends SettingsBase {

  /**
   * {@inheritdoc}
   *
   * Instance settings are settings that are set both in the base form and the
   * variation form. They are editable in both forms and the values are merged
   * together.
   */
  protected function buildForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildForm($form, $form_state);

    $tooltip = new Tooltip('Preview Tooltip');
    $form['preview'] = [
      '#markup' => '<div class="btn w-full text-center">' . $this->t('Preview') . '</div>',
    ];
    $tooltip->applyTo($form['preview']);

    $form['placement'] = [
      '#type' => 'select',
      '#title' => $this->t('Placement'),
      '#description' => $this->t('The default placement of the tooltip.'),
      '#options' => $tooltip->getPlacements(),
      '#required' => TRUE,
      '#default_value' => $this->getValue('placement'),
    ];

    $form['animation'] = [
      '#type' => 'select',
      '#title' => $this->t('Animation'),
      '#description' => $this->t('The default animation of the tooltip.'),
      '#options' => $tooltip->getAnimations(),
      '#required' => TRUE,
      '#default_value' => $this->getValue('animation'),
    ];

    $form['trigger'] = [
      '#type' => 'select',
      '#title' => $this->t('Trigger'),
      '#description' => $this->t('The default trigger of the tooltip.'),
      '#options' => $tooltip->getTriggers(),
      '#required' => TRUE,
      '#default_value' => $this->getValue('trigger'),
    ];

    $form['color'] = [
      '#type' => 'neo_color',
      '#title' => $this->t('Color'),
      '#description' => $this->t('The default color of the tooltip.'),
      '#required' => TRUE,
      '#default_value' => $this->getValue('color'),
    ];

    $form['border_color'] = [
      '#type' => 'neo_color',
      '#title' => $this->t('Border Color'),
      '#description' => $this->t('Optional. Worth setting when the tooltip colour is light enough to need separating from the surface behind it; leave empty for no border.'),
      '#default_value' => $this->getValue('border_color'),
      '#empty_option' => $this->t('No border'),
    ];

    $form['border_radius'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Border Radius'),
      '#description' => $this->t('The radius of the tooltip corners in px or rem. Example: 4px. Leave empty to follow the form item radius.'),
      '#default_value' => $this->getValue('border_radius'),
    ];

    // Flat, not grouped in a details. The settings form is built with
    // `#tree`, so a wrapper element becomes a level in the saved config —
    // every other field in this plugin sits at the top level and these have to
    // as well, or `getValue('description_mode')` reads a key that is not there.
    $form['description_mode'] = [
      '#type' => 'select',
      '#title' => $this->t('Show form descriptions as'),
      '#options' => [
        ElementProcess::DESCRIPTION_TOOLTIP => $this->t('Tooltip — every description moves into a tooltip'),
        ElementProcess::DESCRIPTION_AUTO => $this->t('Automatic — short descriptions become tooltips, long ones stay inline'),
        ElementProcess::DESCRIPTION_INLINE => $this->t('Inline — descriptions render under the field, as Drupal renders them'),
      ],
      '#required' => TRUE,
      '#default_value' => $this->getValue('description_mode'),
      '#description' => $this->t('A description moved into a tooltip is still rendered for screen readers, so it is announced either way.'),
    ];

    // The `#tree` above is why these selectors carry the `instance` wrapper:
    // #states matches on the rendered input name, not on the form key. This
    // plugin sets `variation_allow = false`, so `instance` is the only wrapper
    // these fields are ever built under.
    $form['description_max_length'] = [
      '#type' => 'number',
      '#title' => $this->t('Automatic length cutoff'),
      '#description' => $this->t('Descriptions of this many characters or fewer become tooltips. Counted on the text a reader sees, ignoring any markup.'),
      '#default_value' => $this->getValue('description_max_length'),
      '#min' => 1,
      '#step' => 1,
      '#states' => [
        'visible' => [
          ':input[name="instance[description_mode]"]' => ['value' => ElementProcess::DESCRIPTION_AUTO],
        ],
      ],
    ];

    $form['description_icon'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Show a help icon beside the label'),
      '#description' => $this->t('Without it a field carrying help looks identical to one that does not, and the only way to find it is to hover every field in turn.'),
      '#default_value' => $this->getValue('description_icon'),
      '#states' => [
        'invisible' => [
          ':input[name="instance[description_mode]"]' => ['value' => ElementProcess::DESCRIPTION_INLINE],
        ],
      ],
    ];

    $form['theme_boilerplate'] = [
      '#type' => 'details',
      '#title' => $this->t('Theme SCSS Boilerplate'),
      '#open' => FALSE,
    ];

    $form['theme_boilerplate']['scss'] = [
      '#type' => 'html_tag',
      '#tag' => 'pre',
      '#value' => ".tippy-box[data-theme~='neo'] {
  @apply bg-default text-black shadow-lg;

  &[data-placement^='top'] > .tippy-arrow::before {
    @apply border-t-default;
  }
  &[data-placement^='bottom'] > .tippy-arrow::before {
    @apply border-b-default;
  }
  &[data-placement^='left'] > .tippy-arrow::before {
    @apply border-l-default;
  }
  &[data-placement^='right'] > .tippy-arrow::before {
    @apply border-r-default;
  }
  .tippy-content {
    @apply p-6 text-base;
  }
}",
    ];
    return $form;
  }

}
