<?php

namespace Drupal\y_lb\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Routing\CurrentRouteMatch;
use Drupal\layout_builder\OverridesSectionStorageInterface;
use Drupal\node\NodeInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides an Node Title block (including potential subtitle data).
 *
 * @Block(
 *   id = "lb_node_title",
 *   admin_label = @Translation("Node Title"),
 *   category = @Translation("Common Block")
 * )
 */
class NodeTitleBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The route provider.
   *
   * @var \Drupal\Core\Routing\CurrentRouteMatch
   */
  protected $currentRouteMatch;

  /**
   * Constructs a new NodeTitleBlock instance.
   *
   * @param array $configuration
   *   The plugin configuration, i.e. an array with configuration values keyed
   *   by configuration option name. The special key 'context' may be used to
   *   initialize the defined contexts by setting it to an array of context
   *   values keyed by context names.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Routing\RouteProviderInterface $route_provider
   *   The route provider.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, CurrentRouteMatch $currentRouteMatch) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->currentRouteMatch = $currentRouteMatch;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('current_route_match')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $node = $this->getNodeEntity();
    if (!$node instanceof NodeInterface) {
      return [];
    }
    $build['title'] = $node->getTitle();
    $build['subtitle'] = $node->hasField('field_subtitle') ? $node->field_subtitle->value : '';

    return $build;
  }

  /**
   * Get Node entity.
   *
   * @return NodeInterface|mixed|null
   *   Node entity or NULL if not found.
   * @throws \Drupal\Component\Plugin\Exception\ContextException
   */
  protected function getNodeEntity() {
    // Retrieve node from the route parameters.
    $node = $this->currentRouteMatch->getParameter('node');
    if (!$node instanceof NodeInterface) {
      // If no node found in route parameters check section storage.
      $section_storage = $this->currentRouteMatch->getParameter('section_storage');
      if ($section_storage instanceof OverridesSectionStorageInterface) {
        $node = $section_storage->getContextValue('entity');
      }
    }

    return $node;
  }

}
