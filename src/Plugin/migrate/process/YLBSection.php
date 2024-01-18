<?php

namespace Drupal\y_lb\Plugin\migrate\process;

use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\layout_builder\Section;
use Drupal\layout_builder\SectionComponent;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\Plugin\MigrationInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Fills in Layout Builder sections.
 *
 * @MigrateProcessPlugin(
 *   id = "lb_sections"
 * )
 */
class YLBSection extends ProcessPluginBase implements ContainerFactoryPluginInterface {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $blockContentStorage;

  /**
   * The service for generating UUID.
   *
   * @var \Drupal\Component\Uuid\Php
   */
  protected $uuidService;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition, MigrationInterface $migration = NULL) {
    $instance = new static(
      $configuration,
      $plugin_id,
      $plugin_definition
    );

    $instance->blockContentStorage = $container->get('entity_type.manager')->getStorage('block_content');
    $instance->uuidService = $container->get('uuid');
    return $instance;
  }


  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    if (!$value) {
      return NULL;
    }

    $sections = [];
    foreach ( $value as $section ) {
      $components = [];
      $layout_id = isset($section['bs_col']) ?  "bootstrap_layout_builder:{$section['bs_col']}" : $section['id'];
      $layout_settings = [
        "label" => $section['label'] ?? '',
        "container_wrapper_classes" => $section['container_wrapper_classes'] ?? '',
        "wrapper_classes" => $section['wrapper_classes'] ?? '',
        "container_wrapper_attributes" => $section['container_wrapper_attributes'] ?? NULL,
        "container_wrapper" => $section['container_wrapper'] ?? [],
        "container_wrapper_bg_color_class" => $section['container_wrapper_bg_color_class'] ?? '',
        "container_wrapper_bg_media" => $section['container_wrapper_bg_media'] ?? NULL,
        "container" => $section['container'] ?? '',
        "section_classes" => $section['section_classes'] ?? '',
        "section_attributes" => $section['section_attributes'] ?? NULL,
        "regions_classes" => $section['regions_classes'] ?? [],
        "regions_attributes" => $section['regions_attributes'] ?? [],
        "breakpoints" => $section['breakpoints'] ?? [],
        "layout_regions_classes" => $section['layout_regions_classes'] ?? [],
        "context_mapping" => $section['context_mapping'] ?? [],
        "remove_gutters" => $section['remove_gutters'] ?? "0",
      ];
      if (!empty($section['components'])) {
        foreach ($section['components'] as $key => $componentConfig) {
          $config = $componentConfig['config'];
          $additional = $componentConfig['additional'] ?? [];
          if(empty($componentConfig['block_config'])) {
            $blocks = $this->blockContentStorage->loadByProperties(['uuid' => $componentConfig['uuid']]);
            if (!$blocks) {
              continue;
            }
            $block = array_shift($blocks);
            $revisionId = $block->getRevisionId();
            $config['block_revision_id'] = $revisionId;
          }

          $uuid = $componentConfig['uuid'] ?? $this->uuidService->generate();
          $component = new SectionComponent($uuid, $componentConfig['region'], $config , $additional);
          $components[$uuid] = $component;
        }
      }
      $section = new Section($layout_id, $layout_settings, $components, $third_party_settings = []);
      $sections[] = $section;
    }
    return $sections;
  }
}
