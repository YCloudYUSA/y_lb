<?php

namespace Drupal\y_lb;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\DependencyInjection\ServiceProviderBase;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Replaces the add to cart message.
 */
class YLbServiceProvider extends ServiceProviderBase {

  /**
   * {@inheritdoc}
   */
  public function alter(ContainerBuilder $container) {
    // Replace the event subscriber to prepare layout.
    if ($container->hasDefinition('layout_builder.element.prepare_layout')) {
      $definition = $container->getDefinition('layout_builder.element.prepare_layout');
      $definition->setClass('Drupal\y_lb\EventSubscriber\PrepareLayout')
        ->addArgument(new Reference('request_stack'));
    }
  }

}
