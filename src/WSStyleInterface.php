<?php

namespace Drupal\y_lb;

use Drupal\Component\Plugin\PluginManagerInterface;

/**
 * Defines the interface for Style plugin managers.
 */
interface WSStyleInterface extends PluginManagerInterface {

  /**
   * Returns global styles.
   *
   * @return array
   *   Global styles array (groups with global=true).
   */
  public function getGlobalStyles(): array;

  /**
   * Returns styles for component.
   *
   * @param string $component
   *   Component name.
   *
   * @return array
   *   List of styles that can be applied to the component.
   */
  public function getStyleForComponent(string $component): array;

  /**
   * Get list of libraries.
   *
   * @param string $group
   *   Group name.
   *
   * @return array
   *   Array of the libraries to be attached to the render.
   */
  public function getLibraries(string $group): array;

}
