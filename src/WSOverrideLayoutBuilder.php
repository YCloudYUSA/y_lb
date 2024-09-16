<?php

namespace Drupal\y_lb;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\DefaultPluginManager;
use Drupal\Core\Plugin\Discovery\ContainerDerivativeDiscoveryDecorator;
use Drupal\Core\Plugin\Discovery\YamlDiscovery;

/**
 * Manages discovery and instantiation of WS override plugins.
 *
 * @see plugin_api
 */
class WSOverrideLayoutBuilder extends DefaultPluginManager implements WSOverrideLayoutBuilderInterface {

  /**
   * The entity type manager service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected EntityTypeManagerInterface $entityTypeManager;

  /**
   * Constructs a new WSOverrideLayoutBuilder object.
   *
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache_backend
   *   The cache backend.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager service.
   */
  public function __construct(
    ModuleHandlerInterface $module_handler,
    CacheBackendInterface $cache_backend,
    EntityTypeManagerInterface $entity_type_manager
  ) {
    $this->moduleHandler = $module_handler;
    $this->setCacheBackend($cache_backend, 'ws_lb_override', ['ws_lb_override']);
    $this->alterInfo('ws_lb_override');
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  protected function getDiscovery() {
    if (!isset($this->discovery)) {
      $this->discovery = new YamlDiscovery('ws_lb_override', $this->moduleHandler->getModuleDirectories());
      $this->discovery = new ContainerDerivativeDiscoveryDecorator($this->discovery);
    }
    return $this->discovery;
  }

  /**
   * {@inheritdoc}
   */
  public function processDefinition(&$definition, $plugin_id) {
    parent::processDefinition($definition, $plugin_id);
  }

  /**
   * {@inheritdoc}
   */
  public function getLibraries(string $component_id): array {
    $libraries = [];

    foreach ($this->getDefinitions() as $definition) {
      if ($definition['block_type'] === $component_id
        && !empty($definition['library'])) {
        $libraries[] = $definition['library'];
      }
    }

    return $libraries;
  }

  /**
   * {@inheritdoc}
   */
  public function overrideLibrary(string $component_id): bool {
    foreach ($this->getDefinitions() as $definition) {
      if ($definition['block_type'] === $component_id) {
        return $definition['override'];
      }
    }
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function isApplicableForColorway(EntityInterface $entity): bool {
    foreach ($this->getDefinitions() as $definition) {
      if (!empty($definition['active_colorways'])) {
        $selected_colorway = $this->getSelectedColorway($entity);
        $active_colorways = $definition['active_colorways'];
        foreach ($active_colorways as $active_colorway) {
          if (str_contains($active_colorway, '*')) {
            $active_colorway = substr($active_colorway, 0, -1);
            if (str_contains($selected_colorway, $active_colorway)) {
              return TRUE;
            }
          }
          else if ($selected_colorway === $active_colorway) {
            return TRUE;
          }
        }
      }
    }
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function isApplicableForComponent(string $component_id): bool {
    foreach ($this->getDefinitions() as $definition) {
      if (!empty($definition['block_type'])) {
        return $definition['block_type'] === $component_id;
      }
    }

    return FALSE;
  }

  /**
   * Gets a value of selected colorway from the node or the view display settings.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   And object on the node.
   *
   * @return string
   *   A value f selected colorway.
   */
  private function getSelectedColorway(EntityInterface $entity): string {
    $stored_ws_styles = $entity?->override_styles?->value
      ? unserialize($entity?->styles?->value)
      : $this->getDefaultWsStyle($entity->bundle());
    return $stored_ws_styles['colorway'];
  }

  /**
   * Gets settings for the selected ws colorway for the passed node type.
   *
   * @param string $node_type
   *   A node type name.
   *
   * @return array
   *   An array of the WS colorways values.
   */
  private function getDefaultWsStyle(string $node_type): array {
    $view_display = $this->entityTypeManager
      ->getStorage('entity_view_display')
      ->load('node.' . $node_type . '.full');
    if (!$view_display) {
      return [];
    }
    $settings = $view_display->getThirdPartySettings('y_lb');
    return $settings['styles'] ?? [];
  }

}
