<?php

namespace Drupal\y_lb;

use Drupal\Component\Plugin\PluginManagerInterface;

/**
 * Defines the interface for Style plugin managers.
 */
interface WSStyleOptionInterface extends PluginManagerInterface {

  /**
   * Get style options by provided group name.
   *
   * @param string $group
   *   Group name.
   *
   * @return array
   *   Array of style options for provided group name.
   */
  public function getStylesByGroup(string $group): array;

  /**
   * Get list of libraries.
   *
   * @param string $group
   *   Group name.
   * @param string $style
   *   Style name.
   *
   * @return array
   *   Array of the libraries to be attached to the render.
   */
  public function getLibraries(string $group, string $style): array;

  /**
   * Get Style Option settings.
   *
   * @param string $group
   *   Group name.
   * @param string $style
   *   Style name.
   *
   * @return array|null
   *   Style option settings or NULL if style hasn't been found.
   */
  public function getStyleOption(string $group, string $style): array|NULL;

}
