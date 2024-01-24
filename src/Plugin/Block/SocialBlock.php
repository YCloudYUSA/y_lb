<?php

namespace Drupal\y_lb\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
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
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
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
      'content' => ' <ul class="list-inline">
          <li><a title="Go to YMCA Facebook" href="#"><i class="fab fa-facebook"></i></a></li>
          <li><a title="Go to YMCA Twitter" href="#"><i class="fab fa-twitter"></i></a></li>
          <li><a title="Go to YMCA Youtube channel" href="#"><i class="fab fa-youtube"></i></a></li>
        </ul>',
    ];
  }

  public function blockForm($form, FormStateInterface $form_state) {
    $form['content'] = [
      '#type' => 'text_format',
      '#format' => 'full_html',
      '#title' => $this->t('Block content'),
      '#default_value' => $this->configuration['content'],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $this->configuration['content'] = $form_state->getValue('content')['value'];
  }

}
