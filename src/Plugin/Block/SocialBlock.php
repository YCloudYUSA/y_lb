<?php

namespace Drupal\y_lb\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\ImmutableConfig;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides the Website name block.
 *
 * @Block(
 *   id = "ws_social",
 *   admin_label = @Translation("Social Block"),
 *   category = @Translation("Common blocks")
 * )
 */
class SocialBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The settings of the social links block from the settings form.
   */
  protected array|ImmutableConfig $settingsSocialLinks;

  /**
   * Constructs a SocialBlock object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $configFactory
   *   The config factory.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, ConfigFactoryInterface $configFactory) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->settingsSocialLinks = $configFactory->get('y_lb.admin.settings');
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('config.factory')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    if (empty($this->configuration['content'])) {
      return [
        '#theme' => 'ws_social_links_block',
        '#facebook' => $this->settingsSocialLinks->get('social_links.facebook'),
        '#twitter' => $this->settingsSocialLinks->get('social_links.twitter'),
        '#youtube' => $this->settingsSocialLinks->get('social_links.youtube'),
        '#instagram' => $this->settingsSocialLinks->get('social_links.instagram'),
        '#linkedin' => $this->settingsSocialLinks->get('social_links.linkedin'),
      ];
    }
    return [
      '#markup' => $this->configuration['content'],
      '#attributes' => ['class' => ['field-block-content', 'field-item']],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'label' => 'Stay Connected',
      'label_display' => TRUE,
    ];
  }

}
