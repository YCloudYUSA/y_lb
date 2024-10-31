<?php

namespace Drupal\y_lb\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a general configuration form for Y LB module.
 */
class SettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'y_lb_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'y_lb.admin.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('y_lb.admin.settings');

    $form['social_links'] = [
      '#type' => 'details',
      '#title' => $this->t('Social Links'),
      '#open' => TRUE,
        '#description' => $this->t('In this section, please add links to your pages on the social networks.<br>
          You can leave the field empty if you don\'t have a page on some network.'),
    ];

    $form['social_links']['facebook'] = [
      '#type' => 'url',
      '#title' => $this->t('Facebook'),
      '#default_value' => $config->get('social_links.facebook') ?? NULL,
      '#pattern' => '^(https?:\/\/)?(?:www\.)?facebook\.com(.+)?',
      '#description' => $this->t('Please add a link to your Facebook page.'),
    ];

    $form['social_links']['twitter'] = [
      '#type' => 'url',
      '#title' => $this->t('X(Twitter)'),
      '#default_value' => $config->get('social_links.twitter') ?? NULL,
      '#pattern' => '^(https?:\/\/)?(?:www\.)?(x|twitter)\.com(.+)?',
      '#description' => $this->t('Please add a link to your X(Twitter) page.'),
    ];

    $form['social_links']['youtube'] = [
      '#type' => 'url',
      '#title' => $this->t('Youtube'),
      '#default_value' => $config->get('social_links.youtube') ?? NULL,
      '#pattern' => '^(https?:\/\/)?(?:www\.)?youtube\.com(.+)?',
      '#description' => $this->t('Please add a link to your Youtube page.'),
    ];

    $form['social_links']['instagram'] = [
      '#type' => 'url',
      '#title' => $this->t('Instagram'),
      '#default_value' => $config->get('social_links.instagram') ?? NULL,
      '#pattern' => '^(https?:\/\/)?(?:www\.)?instagram\.com(.+)?',
      '#description' => $this->t('Please add a link to your Instagram page.'),
    ];

    $form['social_links']['linkedin'] = [
      '#type' => 'url',
      '#title' => $this->t('Linkedin'),
      '#default_value' => $config->get('social_links.linkedin') ?? NULL,
      '#pattern' => '^(https?:\/\/)?(?:www\.)?linkedin\.com(.+)?',
      '#description' => $this->t('Please add a link to your LinkedIn page.'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = $this->config('y_lb.admin.settings');
    $config->set('social_links.facebook', $form_state->getValue('facebook'));
    $config->set('social_links.twitter', $form_state->getValue('twitter'));
    $config->set('social_links.youtube', $form_state->getValue('youtube'));
    $config->set('social_links.instagram', $form_state->getValue('instagram'));
    $config->set('social_links.linkedin', $form_state->getValue('linkedin'));
    $config->save();
    parent::submitForm($form, $form_state);
  }

}
