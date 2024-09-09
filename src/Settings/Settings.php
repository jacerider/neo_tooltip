<?php

namespace Drupal\neo_tooltip\Settings;

use Drupal\Core\Form\FormStateInterface;
use Drupal\neo_settings\Plugin\SettingsBase;
use Drupal\neo_tooltip\Tooltip;

/**
 * Module settings.
 *
 * @Settings(
 *   id = "neo_tooltip",
 *   label = @Translation("Neo Tooltip"),
 *   config_name = "neo_tooltip.settings",
 *   menu_title = @Translation("Tooltip"),
 *   route = "/admin/config/neo/neo-tooltip",
 *   admin_permission = "administer neo_tooltip",
 *   variation_allow = false,
 *   variation_conditions = false,
 *   variation_ordering = false,
 * )
 */
class Settings extends SettingsBase {

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

    $form['theme_boilerplate'] = [
      '#type' => 'details',
      '#title' => $this->t('Theme SCSS Boilerplate'),
      '#open' => FALSE,
    ];

    $form['theme_boilerplate']['scss'] = [
      '#type' => 'html_tag',
      '#tag' => 'pre',
      '#value' => "
.tippy-box[data-theme~='neo'] {
  @apply bg-white text-black shadow-lg;

  &[data-placement^='top'] > .tippy-arrow::before {
    @apply border-t-white;
  }
  &[data-placement^='bottom'] > .tippy-arrow::before {
    @apply border-b-white;
  }
  &[data-placement^='left'] > .tippy-arrow::before {
    @apply border-l-white;
  }
  &[data-placement^='right'] > .tippy-arrow::before {
    @apply border-r-white;
  }
  .tippy-content {
    @apply p-6 text-base;
  }
}
      ",
    ];
    return $form;
  }

}
