<?php

namespace Drupal\y_lb;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\DefaultPluginManager;
use Drupal\Core\Plugin\Discovery\ContainerDerivativeDiscoveryDecorator;
use Drupal\Core\Plugin\Discovery\YamlDiscovery;
use Drupal\y_lb\Event\WSStyleGroupAppliesToAlter;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 * Manages discovery and instantiation of style plugins (style groups).
 *
 * @see plugin_api
 */
class WSStyleManager extends DefaultPluginManager implements WSStyleInterface {

  /**
   * The event dispatcher.
   *
   * @var \Symfony\Contracts\EventDispatcher\EventDispatcherInterface
   */
  protected $eventDispatcher;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Constructs a new WSStyleManager object.
   *
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache_backend
   *   The cache backend.
   * @param \Symfony\Contracts\EventDispatcher\EventDispatcherInterface $event_dispatcher
   *   The event dispatcher.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   */
  public function __construct(ModuleHandlerInterface $module_handler, CacheBackendInterface $cache_backend, EventDispatcherInterface $event_dispatcher, EntityTypeManagerInterface $entity_type_manager) {
    $this->moduleHandler = $module_handler;
    $this->eventDispatcher = $event_dispatcher;
    $this->entityTypeManager = $entity_type_manager;
    $this->setCacheBackend($cache_backend, 'ws_style', ['ws_style']);
    $this->alterInfo('ws_style');
  }

  /**
   * {@inheritdoc}
   */
  protected function getDiscovery() {
    if (!isset($this->discovery)) {
      $this->discovery = new YamlDiscovery('ws_style', $this->moduleHandler->getModuleDirectories());
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
  public function getGlobalStyles(): array {
    $styles = [];
    foreach ($this->getDefinitions() as $style) {
      if (empty($style['global'])) {
        continue;
      }
      $styles[$style['name']] = $style['label'];
    }

    return $styles;
  }

  /**
   * {@inheritdoc}
   */
  public function getStyleForComponent(string $component): array {
    $groups = [];

    // Normalize component (fix for reusable blocks)
    $normalized_component = $this->normalizeComponent($component);

    // Dispatch event to alter applies to components.
    $event = new WSStyleGroupAppliesToAlter($component);
    $this->eventDispatcher->dispatch($event, WSStyleGroupAppliesToAlter::ALTER_APPLIES_TO);
    $component_groups = $event->getGroups();

    foreach ($this->getDefinitions() as $group) {
      $applies_to = $group['applies_to'];

      if (
        in_array($component, $applies_to) ||
        in_array($normalized_component, $applies_to) ||
        in_array($group['name'], $component_groups)
      ) {
        $groups[$group['name']] = $group['label'];
      }
    }

    return $groups;
  }

  /**
   * {@inheritdoc}
   */
  public function getLibraries(string $group): array {
    $libraries = [];

    foreach ($this->getDefinitions() as $definition) {
      if ($definition['name'] == $group
        && !empty($definition['library'])) {
        $libraries[] = $definition['library'];
      }
    }

    return $libraries;
  }

  /**
   * Normalize component identifiers.
   *
   * Converts:
   *   block_content:{uuid} → block_content:{bundle}
   */
  protected function normalizeComponent(string $component): string {
    if (!str_starts_with($component, 'block_content:')) {
      return $component;
    }

    [, $identifier] = explode(':', $component, 2);

    // Quick UUID detection.
    if (!preg_match('/^[0-9a-fA-F-]{36}$/', $identifier)) {
      return $component;
    }

    $blocks = $this->entityTypeManager
      ->getStorage('block_content')
      ->loadByProperties(['uuid' => $identifier]);

    if (empty($blocks)) {
      return $component;
    }

    $block = reset($blocks);

    return 'block_content:' . $block->bundle();
  }

}
