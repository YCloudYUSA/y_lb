<?php

namespace Drupal\y_lb;

use Drupal\Core\Cache\CacheBackendInterface;
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
   * Constructs a new WSStyleManager object.
   *
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache_backend
   *   The cache backend.
   * @param \Symfony\Contracts\EventDispatcher\EventDispatcherInterface $event_dispatcher
   *   The event dispatcher.
   */
  public function __construct(ModuleHandlerInterface $module_handler, CacheBackendInterface $cache_backend, EventDispatcherInterface $event_dispatcher) {
    $this->moduleHandler = $module_handler;
    $this->eventDispatcher = $event_dispatcher;
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
    // Dispatch event to alter applies to components.
    $event = new WSStyleGroupAppliesToAlter($component);
    $this->eventDispatcher->dispatch($event, WSStyleGroupAppliesToAlter::ALTER_APPLIES_TO);
    $component_groups = $event->getGroups();

    foreach ($this->getDefinitions() as $group) {
      $applies_to = $group['applies_to'];

      if (in_array($component, $applies_to)
        || in_array($group['name'], $component_groups)) {
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

}
