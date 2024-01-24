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
 *   id = "ws_copyright",
 *   admin_label = @Translation("Copyright Block"),
 *   category = @Translation("Common blocks")
 * )
 */
class CopyrightBlock extends BlockBase implements ContainerFactoryPluginInterface {

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
      'label_display' => FALSE,
      'content' => '<p>Copyright© YMCA of Location. All rights reserved. The YMCA of Location is a 501(c)(3) not-for-profit social services organization dedicated to Youth Development, Healthy Living, and Social Responsibility.</p>',
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
