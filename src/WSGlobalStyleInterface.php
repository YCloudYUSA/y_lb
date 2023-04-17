<?php

namespace Drupal\y_lb;

use Drupal\Component\Plugin\PluginManagerInterface;

/**
 * Defines the interface for Global Style plugin managers.
 */
interface WSGlobalStyleInterface extends PluginManagerInterface {

  /**
   * Gets list of registered global styles.
   *
   * @return array
   *   The skins.
   */
  public function getGlobalStyles(): array;

  /**
   * Gets the libraries filtered by skin name and paragraph keys.
   *
   * @param string $component
   *   The component machine name.
   *
   * @return array
   *   The definitions
   */
  public function getStyleForComponent(string $component = ''): array;

}
