<?php

namespace Drupal\y_lb\Event;

use Drupal\Component\EventDispatcher\Event;

/**
 * Event to alter available WS Style Groups for component.
 */
class WSStyleGroupAppliesToAlter extends Event {

  /**
   * Event ID.
   */
  const ALTER_APPLIES_TO = 'y_lb.ws_style_group_applies_to_alter';

  /**
   * Component ID.
   *
   * @var string
   */
  protected $component;

  /**
   * List of the WS Style Groups that are available for the component.
   *
   * @var array
   */
  protected $groups;

  /**
   * Constructs a WSComponentStyleOptionAlter object.
   *
   * @param string $component
   *   Component ID.
   */
  public function __construct(string $component) {
    $this->component = $component;
    $this->groups = [];
  }

  /**
   * Get Component ID.
   *
   * @return string
   *   Component ID.
   */
  public function getComponent():string {
    return $this->component;
  }

  /**
   * Get array of the WS Style Groups that are available for the component.
   *
   * @return array
   *   WS Style Groups available for the component.
   */
  public function getGroups():array {
    return $this->groups;
  }

  /**
   * Set WS Style Groups method.
   *
   * @param array $groups
   *   Available WS Style Groups.
   *
   * @return void
   */
  public function setGroups(array $groups) {
    $this->groups = array_merge($this->groups, $groups);
  }

}
