<?php

namespace Drupal\y_lb\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Extension\ModuleExtensionList;
use Drupal\Core\File\FileUrlGeneratorInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Routing\CurrentRouteMatch;
use Drupal\Core\Url;
use Drupal\file\Entity\File;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides the Site Logo block.
 *
 * @Block(
 *   id = "ws_site_logo",
 *   admin_label = @Translation("Site Logo Block"),
 *   category = @Translation("Common blocks")
 * )
 */
class SiteLogoBlock extends BlockBase implements ContainerFactoryPluginInterface {
  /**
   * The route provider.
   *
   * @var \Drupal\Core\Routing\CurrentRouteMatch
   */
  protected $currentRouteMatch;

  /**
   * Cuurent node.
   *
   * @var \Drupal\node\Entity\Node
   */
  protected $node;

  /**
   * Contains the configuration object factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * The file URL generator.
   *
   * @var \Drupal\Core\File\FileUrlGeneratorInterface
   */
  protected $fileUrlGenerator;

  /**
   * The module list service.
   *
   * @var \Drupal\Core\Extension\ModuleExtensionList
   */
  protected $moduleList;

  /**
   * Constructs a new NodeTagsBlock instance.
   *
   * @param array $configuration
   *   The plugin configuration, i.e. an array with configuration values keyed
   *   by configuration option name. The special key 'context' may be used to
   *   initialize the defined contexts by setting it to an array of context
   *   values keyed by context names.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Routing\RouteProviderInterface $route_provider
   *   The route provider.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The factory for configuration objects.
   * @param \Drupal\Core\File\FileUrlGeneratorInterface $file_url_generator
   *   The file URL generator.
   * @param \Drupal\Core\Extension\ModuleExtensionList $module_list
   *   The module list service.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, CurrentRouteMatch $currentRouteMatch, ConfigFactoryInterface $config_factory, FileUrlGeneratorInterface $file_url_generator = NULL, ModuleExtensionList $module_list = NULL) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->currentRouteMatch = $currentRouteMatch;
    $this->node = $currentRouteMatch->getParameter('node');
    $this->configFactory = $config_factory;
    $this->fileUrlGenerator = $file_url_generator;
    $this->moduleList = $module_list;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('current_route_match'),
      $container->get('config.factory'),
      $container->get('file_url_generator'),
      $container->get('extension.list.module')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'logo_type' => 'white',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form['logo_type'] = [
      '#type' => 'select',
      '#title' => $this->t('WS Site logo'),
      '#options' => [
        'theme' => $this->t('Theme logo'),
        'colorway' => $this->t('Colorway logo'),
        'white' => $this->t('White logo'),
      ],
      '#default_value' => $this->configuration['logo_type'],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $this->configuration['logo_type'] = $form_state->getValue('logo_type');
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build['#site_slogan'] = $this->configFactory->get('system.site')->get('slogan');
    $build['#front_page'] = Url::fromRoute('<front>')->toString();
    $type_of_logo = $this->configuration['logo_type'];
    if ($type_of_logo === 'theme') {
      $this->ylbPreprocessPageLogo($build);
    }
    else {
      $path = $this->moduleList
        ->getPath('y_lb') . '/assets/svg/logo_' . $type_of_logo . '.svg';
      $logo_url = $this->fileUrlGenerator
        ->generateAbsoluteString($path);
      $build['#site_logo_is_svg'] = TRUE;
      $build['#site_logo_svg'] = file_get_contents($path);
      $build['#logo_url'] = $logo_url;
    }
    return $build;
  }

  /**
   * Helper function for preprocess logo.
   *
   * @param $build
   */
  private function ylbPreprocessPageLogo(&$build) {

    $build['#logo_url'] = theme_get_setting('logo.path') ?
      $this->fileUrlGenerator->generateAbsoluteString(theme_get_setting('logo.path'))
      : '';
    $build['#transparent_logo_url'] = $build['mobile_logo_url'] = $build['#logo_url'];

    if ($build['#logo_url']) {
      $this->ylbPreprocessSvgLogo($build);
    }
    if ($this->node) {
      if ($this->node->getType() === 'program') {
        $build['#logo_url'] = $this->ylbGetThemeColoredLogo();
      }

      if (\Drupal::hasService('openy_loc_camp.camp_service')) {
        $this->ylbPreprocessLogoForCampsNode($build);
      }
    }
  }

  /**
   * Get data from SVG file.
   */
  private function ylbPreprocessSvgLogo(&$build) {
    // If logo is a SVG lets load it content, so we can inline it.
    if ($build['#site_logo_is_svg'] = strlen($build['#logo_url']) - strpos($build['#logo_url'], '.svg') === 4) {
      $build['#site_logo_svg'] = file_get_contents(DRUPAL_ROOT . parse_url($build['#logo_url'], PHP_URL_PATH));
    }
  }

  /**
   * Prepare a logo data for camp nodes.
   */
  private function ylbPreprocessLogoForCampsNode(&$build) {
    if (\Drupal::service('openy_loc_camp.camp_service')->nodeHasOrIsCamp($this->node)) {
      if ($camp_logo_uri = $this->getFileUriByFid(theme_get_setting('openy_carnation_camp_section_logo'))) {
        $build['#logo_url'] = $this->fileUrlGenerator->generateAbsoluteString($camp_logo_uri);
      }
      $build['#camp_favicon'] = $this->fileUrlGenerator->generateAbsoluteString($this->getFileUriByFid(theme_get_setting('openy_carnation_camp_favicon')));
    }
  }

  /**
   * Determine colored logo that uploaded in the theme settings.
   */
  private function ylbGetThemeColoredLogo() {
    if ($colored_logo = $this->getFileUriByFid(theme_get_setting('colored_logo'))) {
      return $this->fileUrlGenerator
        ->generateAbsoluteString($colored_logo);
    }
    else {
      $path = $this->moduleList->getPath('y_lb') . '/assets/svg/logo_blue.svg';
      return $this->fileUrlGenerator
        ->generateAbsoluteString($path);
    }
  }

  /**
   * Get an file URI for the logo tact uploaded in the theme settings.
   *
   * @param array $fid_array
   *   An file object array.
   *
   * @return string
   */
  private function getFileUriByFid(array $fid_array) {
    $file = NULL;
    if (!empty($fid_array) && is_numeric(reset($fid_array))) {
      $file = File::load(reset($fid_array));
    }

    if ($file) {
      return $file->getFileUri();
    }

    return '';
  }

}
