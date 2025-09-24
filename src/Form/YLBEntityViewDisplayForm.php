<?php

namespace Drupal\y_lb\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\layout_builder\Entity\LayoutEntityDisplayInterface;
use Drupal\layout_builder\Form\LayoutBuilderEntityViewDisplayForm;

/**
 * Provides a custom form for the Y Layout Builder settings for entity display.
 */
class YLBEntityViewDisplayForm extends LayoutBuilderEntityViewDisplayForm {

  /**
   * {@inheritDoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    if ($this->isCanonicalMode($this->entity->getMode())) {
      $entity_type = $this->entityTypeManager->getDefinition($this->entity->getTargetEntityTypeId());
      $form = parent::form($form, $form_state);
      $form['layout']['allow_style'] = [
        '#type' => 'checkbox',
        '#title' => $this->t('Allow each @entity to have its style customized.', [
          '@entity' => $entity_type->getSingularLabel(),
        ]),
          '#default_value' => $this->entity->getThirdPartySetting('y_lb', 'allow_style', FALSE),
      ];
      $form['#entity_builders']['layout_builder'] = '::entityFormEntityBuild';
    }
    return $form;
  }

  /**
   * {@inheritDoc}
   */
  public function entityFormEntityBuild($entity_type_id, LayoutEntityDisplayInterface $display, &$form, FormStateInterface &$form_state) {
    parent::entityFormEntityBuild($entity_type_id, $display, $form, $form_state);
    $allow_style = (bool) $form_state->getValue(['layout', 'allow_style'], FALSE);
    $display->setThirdPartySetting('y_lb', 'allow_style', $allow_style);
  }

}
