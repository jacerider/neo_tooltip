<?php

declare(strict_types=1);

namespace Drupal\neo_tooltip\EventSubscriber;

use Drupal\neo_build\Event\NeoBuildInlineEvent;
use Drupal\neo_settings\SettingsRepositoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Act on build events.
 *
 * @package Drupal\custom_events\EventSubscriber
 */
class NeoBuildInlineEventSubscriber implements EventSubscriberInterface {

  /**
   * The settings.
   *
   * @var \Drupal\neo_settings\Plugin\SettingsInterface
   */
  protected $settings;

  /**
   * Constructs a new NeoBuildEventSubscriber object.
   */
  public function __construct(
    SettingsRepositoryInterface $settings,
  ) {
    $this->settings = $settings->getActive();
  }

  /**
   * Subscribe to the Neo build event dispatched.
   *
   * We inject the CSS variables directly into the DOM so that we do not need
   * to wait for the build to complete before the CSS is applied.
   *
   * @param \Drupal\neo_build\Event\NeoBuildInlineEvent $event
   *   The neo build dev event.
   */
  public function onInlineBuild(NeoBuildInlineEvent $event) {
    if ($color = $this->settings->getValue('color')) {
      $event->addCssValue('--tooltip-bg', 'var(--color-' . $color . ')');
      $event->addCssValue('--tooltip-text', 'var(--color-' . $color . '-content)');
    }
    // A border only earns its keep once the tooltip is light enough to need
    // separating from what it sits on, so it is opt-in: leaving the colour
    // unset leaves the width at the stylesheet's 0 and costs nothing.
    if ($borderColor = $this->settings->getValue('border_color')) {
      $event->addCssValue('--tooltip-border', 'rgb(var(--color-' . $borderColor . '))');
      $event->addCssValue('--tooltip-border-width', '1px');
    }
    if ($radius = $this->settings->getValue('border_radius')) {
      $event->addCssValue('--tooltip-radius', $radius);
    }
    $event->addCacheTags(['config:neo_tooltip.settings']);
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    return [
      NeoBuildInlineEvent::EVENT_NAME => 'onInlineBuild',
    ];
  }

}
