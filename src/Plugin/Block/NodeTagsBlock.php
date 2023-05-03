<?php

namespace Drupal\y_lb\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Routing\CurrentRouteMatch;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides Tags block.
 *
 * @Block(
 *   id = "lb_node_tags",
 *   admin_label = @Translation("Tags Block"),
 *   category = @Translation("Common blocks")
 * )
 */
class NodeTagsBlock extends BlockBase implements ContainerFactoryPluginInterface {
  /**
   * The route provider.
   *
   * @var \Drupal\Core\Routing\CurrentRouteMatch
   */
  protected $currentRouteMatch;

  /**
   * Cuurent node.
   *
   * @var \Drupal\node\Entity\Node
   */
  protected $node;

  /**
   * Constructs a new NodeTagsBlock instance.
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
    $this->node = $currentRouteMatch->getParameter('node');
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
    if (!$this->node || !$this->node->hasField('field_tags')) {
      return [];
    }

    $build['tags'] = $this->node->get('field_tags')->view([
      'label' => 'hidden',
      'settings' => [
        'link' => FALSE,
      ],
    ]);
    return $build;
  }

}
