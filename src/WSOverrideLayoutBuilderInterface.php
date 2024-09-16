<?php

namespace Drupal\y_lb;

use Drupal\Component\Plugin\PluginManagerInterface;
use Drupal\Core\Entity\EntityInterface;

/**
 * Defines the interface for WS Override LB plugin managers.
 */
interface WSOverrideLayoutBuilderInterface extends PluginManagerInterface {

  /**
   * Get list of libraries.
   *
   * @param string $component_id
   *   Component ID (for ex: lb_tabs or ws_logo etc.).
   *
   * @return array
   *   Array of the libraries to be attached to the render.
   */
  public function getLibraries(string $component_id): array;

  /**
   * Compares a selected colorway for the page and an active colorway from the plugin.
   *
   * @param EntityInterface $entity
   *   A node object.
   *
   * @return bool
   *   Returns TRUE if the node's colorway is the same as determined in the plugin definition.
   */
  public function isApplicableForColorway(EntityInterface $entity): bool;

  /**
   * Checks if a plugin can be applied for this component by its ID.
   *
   * @param string $component_id
   *   Component ID (for ex: lb_tabs or ws_logo etc.).
   *
   * @return bool
   *   Returns TRUE if the component ID is the same as determined in the plugin definition.
 */
  public function isApplicableForComponent(string $component_id): bool;

  /**
   * Checks override status.
   *
   * @param string $component_id
   *   Component ID (for ex: lb_tabs or ws_logo etc.).
   *
   * @return bool
   *   Returns TRUE if in the plugin definition the override flag is TRUE for component.
   */
  public function overrideLibrary(string $component_id): bool;
}
