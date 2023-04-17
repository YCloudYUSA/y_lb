<?php

namespace Drupal\y_lb;

use Drupal\Component\Plugin\Exception\PluginException;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\DefaultPluginManager;
use Drupal\Core\Plugin\Discovery\ContainerDerivativeDiscoveryDecorator;
use Drupal\Core\Plugin\Discovery\YamlDiscovery;


/**
 * Manages discovery and instantiation of style plugins.
 *
 * @see plugin_api
 */
class WSGlobalStyleManager extends DefaultPluginManager implements WSGlobalStyleInterface {

  /**
   * Default values for each workflow_group plugin.
   *
   * @var array
   */
  protected $defaults = [];

  /**
   * Constructs a new ParagraphSkins object.
   *
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache_backend
   *   The cache backend.
   */
  public function __construct(ModuleHandlerInterface $module_handler, CacheBackendInterface $cache_backend) {
    $this->moduleHandler = $module_handler;
    $this->setCacheBackend($cache_backend, 'ws_global_style', ['ws_global_style']);
    $this->alterInfo('ws_global_style');
  }

  /**
   * {@inheritdoc}
   */
  protected function getDiscovery() {
    if (!isset($this->discovery)) {
      $this->discovery = new YamlDiscovery('ws_global_style', $this->moduleHandler->getModuleDirectories());
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
   * Returns list of skins for paragraph.
   *
   * @param string $type_id Paragraph type id
   *
   * @return array
   */
  public function getGlobalStyles(): array {
    $definitions = $this->getDefinitions();



    return $definitions;
  }

  /**
   * Returns get styles for component.
   *
   * @param string $component
   *   Component machine name
   *
   * @return array
   */
  public function getStyleForComponent(string $component = ''): array {
    $definitions = $this->getDefinitions();

    return [];
  }

}
