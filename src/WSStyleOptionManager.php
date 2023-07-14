<?php

namespace Drupal\y_lb;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ExtensionPathResolver;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\File\FileUrlGeneratorInterface;
use Drupal\Core\Plugin\DefaultPluginManager;
use Drupal\Core\Plugin\Discovery\ContainerDerivativeDiscoveryDecorator;
use Drupal\Core\Plugin\Discovery\YamlDiscovery;

/**
 * Manages discovery and instantiation of style plugins.
 *
 * @see plugin_api
 */
class WSStyleOptionManager extends DefaultPluginManager implements WSStyleOptionInterface {

  /**
   * The extension path resolver.
   *
   * @var \Drupal\Core\Extension\ExtensionPathResolver
   */
  protected $extensionPathResolver;

  /**
   * The file URL generator.
   *
   * @var \Drupal\Core\File\FileUrlGeneratorInterface
   */
  protected $fileUrlGenerator;

  /**
   * Constructs a new WSStyleOptionManager object.
   *
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache_backend
   *   The cache backend.
   * @param \Drupal\Core\Extension\ExtensionPathResolver $extension_path_resolver
   *   The extension path resolver.
   * @param \Drupal\Core\File\FileUrlGeneratorInterface $file_url_generator
   *   The file URL generator.
   */
  public function __construct(ModuleHandlerInterface $module_handler, CacheBackendInterface $cache_backend, ExtensionPathResolver $extension_path_resolver, FileUrlGeneratorInterface $file_url_generator) {
    $this->moduleHandler = $module_handler;
    $this->extensionPathResolver = $extension_path_resolver;
    $this->fileUrlGenerator = $file_url_generator;
    $this->setCacheBackend($cache_backend, 'ws_style_option', ['ws_style_option']);
    $this->alterInfo('ws_style_option');
  }

  /**
   * {@inheritdoc}
   */
  protected function getDiscovery() {
    if (!isset($this->discovery)) {
      $this->discovery = new YamlDiscovery('ws_style_option', $this->moduleHandler->getModuleDirectories());
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
  public function getStylesByGroup(string $group): array {
    $options = [];

    foreach ($this->getDefinitions() as $module_options) {
      $provider = $module_options['provider'];
      $module_options = array_filter($module_options, 'is_int', ARRAY_FILTER_USE_KEY);
      foreach ($module_options as $option) {
        if ($option['group'] == $group) {
          $options[$option['name']] = $this->processStyleOption($provider, $option);
        }
      }
    }

    return $options;
  }

  /**
   * Process Style option data.
   *
   * @param string $module_name
   *   Provider module name.
   * @param array $option
   *   Option data.
   *
   * @return array
   *   Processed option data.
   */
  protected function processStyleOption(string $module_name, array $option) {
    $option['provider'] = $module_name;
    if (!empty($option['image'])) {
      $module_path = $this->extensionPathResolver->getPath('module', $module_name);
      $image_path = $this->fileUrlGenerator->generateAbsoluteString($module_path . $option['image']);
      if (!empty($image_path)) {
        $option['image'] = $image_path;
      }
    }

    return $option;
  }

  /**
   * {@inheritdoc}
   */
  public function getLibraries(string $group, string $style): array {
    $libraries = [];

    foreach ($this->getDefinitions() as $module_options) {
      $module_options = array_filter($module_options, 'is_int', ARRAY_FILTER_USE_KEY);
      foreach ($module_options as $style_option) {
        if ($style_option['group'] == $group
          && $style_option['name'] == $style) {
          $libraries[] = $style_option['library'];
        }
      }
    }

    return $libraries;
  }

  /**
   * {@inheritdoc}
   */
  public function getStyleOption(string $group, string $style): array|NULL {
    foreach ($this->getDefinitions() as $module_options) {
      $module_options = array_filter($module_options, 'is_int', ARRAY_FILTER_USE_KEY);
      foreach ($module_options as $style_option) {
        if ($style_option['group'] == $group
          && $style_option['name'] == $style) {
          return $style_option;
        }
      }
    }

    return NULL;
  }


  /**
   * {@inheritdoc}
   */
  public function getClasses(string $group, string $style): array {
    $classes = [];
    $style_option = $this->getStyleOption($group, $style);

    if ($style_option && !empty($style_option['class'])) {
      $classes[] =  $style_option['class'];
    }

    return $classes;
  }

}
