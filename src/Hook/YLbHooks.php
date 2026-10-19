<?php

namespace Drupal\y_lb\Hook;

use Drupal\Core\Extension\ProceduralCall;
use Drupal\Core\Hook\Attribute\ReorderHook;
use Drupal\Core\Hook\Order\Order;

/**
 * Hook implementations for y_lb.
 */
#[ReorderHook('form_alter', class: ProceduralCall::class, method: 'inline_entity_form_form_alter', order: Order::Last)]
class YLbHooks {

}
