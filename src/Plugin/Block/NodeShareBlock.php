<?php

namespace Drupal\y_lb\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Routing\CurrentRouteMatch;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides Share block.
 *
 * @Block(
 *   id = "lb_node_share",
 *   admin_label = @Translation("Share Block"),
 *   category = @Translation("Common blocks")
 * )
 */
class NodeShareBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The route provider.
   *
   * @var \Drupal\Core\Routing\CurrentRouteMatch
   */
  protected $currentRouteMatch;

  /**
   * The module handler.
   *
   * @var \Drupal\Core\Extension\ModuleHandlerInterface
   */
  protected $moduleHandler;

  /**
   * Cuurent node.
   *
   * @var \Drupal\node\Entity\Node
   */
  protected $node;

  /**
   * Constructs a new NodeShareBlock instance.
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
  public function __construct(array $configuration, $plugin_id, $plugin_definition, CurrentRouteMatch $currentRouteMatch, ModuleHandlerInterface $module_handler) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->currentRouteMatch = $currentRouteMatch;
    $this->moduleHandler = $module_handler;
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
      $container->get('current_route_match'),
      $container->get('module_handler')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    if (!$this->node) {
      return [];
    }

    $url = urlencode($this->node->toUrl('canonical', ['absolute' => TRUE])->toString());
    // When adding share options here, also add them to assets/scss/bs-icons.
    $links = [
      'facebook' => 'https://www.facebook.com/sharer.php?u=' . $url,
      'twitter-x' => 'https://twitter.com/intent/tweet?url=' . $url,
      'linkedin'  => 'https://www.linkedin.com/shareArticle?mini=true&url=' . $url,
    ];

    $this->moduleHandler->alter('lb_node_share_block_links', $links);

    $build = [
      '#links' => $links,
    ];
    return $build;
  }

}
