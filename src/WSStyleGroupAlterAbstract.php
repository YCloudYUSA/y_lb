<?php

namespace Drupal\y_lb;

use Drupal\y_lb\Event\WSStyleGroupAppliesToAlter;

/**
 * An event subscriber to alter available WS Style groups for a component.
 */
abstract class WSStyleGroupAlterAbstract {

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    return [
      WSStyleGroupAppliesToAlter::ALTER_APPLIES_TO => 'alterGroups'
    ];
  }

  /**
   * Alter WS Style Groups applies to option for provided component.
   *
   * @param WSStyleGroupAppliesToAlter $event
   *   Event object.
   *
   * @return void
   */
  public function alterGroups(WSStyleGroupAppliesToAlter $event) {
    $component = $event->getComponent();
    $allowed_groups = $this->getAllowedStyleGroups();
    if (!empty($allowed_groups[$component])) {
      $event->setGroups($allowed_groups[$component]);
    }
  }

  /**
   * Get WS Style Groups allowed to the component.
   *
   * @return array[]
   *  Components allowed WS Style Groups.
   */
  abstract protected function getAllowedStyleGroups();

}
