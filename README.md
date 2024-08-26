CONTENTS OF THIS FILE
---------------------

 * Introduction
 * Requirements
 * Installation
 * Usage within TWIG
 * Usage within PHP
 * Options


INTRODUCTION
------------

Provide tooltip API for elements and fields. It uses the Tippy.js library.

https://atomiks.github.io/tippyjs/v6/getting-started/


REQUIREMENTS
------------

This module requires no modules outside of Drupal core.


INSTALLATION
------------

Install as you would normally install a contributed Drupal module. Visit
https://www.drupal.org/node/1897420 for further information.


USAGE WITHIN TWIG
-----

Simple text:

```twig
<a{{ attributes.addClass(classes)|neo_tooltip_trigger }}>
  Your content.
  {{ 'Tooltip content'|neo_tooltip_content }}
</a>
```

Build array:

```twig
<a{{ attributes.addClass(classes)|neo_tooltip_trigger({theme: 'badge', placement: 'right', animation: 'shift-toward'}) }}>
  Your content.
  {{ content|neo_tooltip_content }}
</a>
```


USAGE WITHIN PHP
-----

```php
// Link element.
$build['link'] = [
  '#type' => 'link',
  '#title' => $this->t('Tooltip trigger'),
  '#url' => Url::fromRoute('<front>'),
];
$content = 'Tooltip content';
$tooltip = new Drupal\neo_tooltip\Tooltip($content);
$tooltip
  ->setAnimationToShiftAway()
  ->setPlacementToBottomStart()
  ->setArrow(FALSE)
  ->setTrigger('focusin');
$tooltip->applyTo($build['link']);

// Markup element.
$build['markup'] = [
  '#markup' => '<p>' . $this->t('This is a markup tooltip.') . '</p>',
];
$tooltip = new Tooltip('Tooltip content');
$tooltip->applyTo($build['markup']);
```


OPTIONS
-----

### animation

```js
{
  // default
  trigger: 'fade',
  // others
  trigger: false,
  trigger: 'shift-away',
  trigger: 'shift-toward',
  trigger: 'scale',
  trigger: 'perspective',
}
```

### trigger

```js
{
  // default
  trigger: 'mouseenter focus',
  // others:
  trigger: 'click',
  trigger: 'focusin',
  trigger: 'mouseenter click',
  // only programmatically trigger it
  trigger: 'manual',
}
```

### placement

```js
{
  // default
  placement: 'top',

  // full list:
  placement: 'top-start',
  placement: 'top-end',

  placement: 'right',
  placement: 'right-start',
  placement: 'right-end',

  placement: 'bottom',
  placement: 'bottom-start',
  placement: 'bottom-end',

  placement: 'left',
  placement: 'left-start',
  placement: 'left-end',

  // choose the side with most space
  placement: 'auto',
  placement: 'auto-start',
  placement: 'auto-end',
}
```
