<?php

namespace Drupal\y_lb\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\layout_builder\Form\DefaultsEntityForm;
use Drupal\layout_builder\SectionStorageInterface;

/**
 * Provides a custom form containing the Layout Builder UI for defaults with WS Styles plugin management.
 */
class YLBEntityViewDisplay extends DefaultsEntityForm {

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, SectionStorageInterface $section_storage = NULL) {
    $settings = $this->entity->getThirdPartySettings('y_lb');
    $form['ws_design_settings'] = [
      '#type' => 'ws_style_select',
      '#default_value' => $settings['styles'],
    ];

    return parent::buildForm($form, $form_state, $section_storage);
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $this->entity->setThirdPartySetting('y_lb', 'styles', $form_state->getValue('ws_design_settings'));
    return parent::save($form, $form_state);
  }

}
