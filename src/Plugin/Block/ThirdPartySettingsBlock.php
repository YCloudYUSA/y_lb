<?php
namespace Drupal\y_lb\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a block that displays ThirdPartySettings value for the current node.
 *
 * @Block(
 *   id = "y_lb_third_party_settings_block",
 *   admin_label = @Translation("Third Party Settings Block"),
 *   category = @Translation("WS Layout Builder"),
 * )
 */
class ThirdPartySettingsBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $node = \Drupal::routeMatch()->getParameter('node');

    // Check if the current page is a node page.
    if (!$node || !($node instanceof \Drupal\node\NodeInterface)) {
      return [];
    }

    // Get the content type of the current node.
    $content_type = $node->getType();
    //$node->setThirdPartySetting('y_lb', 'my_key', 'my_value');
    $entity_display = \Drupal::entityTypeManager()
      ->getStorage('entity_view_display')
      ->load('node.' . $content_type . '.default');
    $thirdPartySettings = $entity_display->getThirdPartySettings('y_lb');
    //$thirdPartySettingsNode = $node->getThirdPartySettings('y_lb');
    return [
      [
      '#markup' =>'<div>Global Settings:'.print_r($thirdPartySettings,1).' </div>',
      ],
      [
        '#markup' =>'<div>Node Settings: </div>',
      ]
    ];
  }

}
