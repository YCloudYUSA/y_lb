<?php

namespace Drupal\y_lb\Drush\Commands;

use Drupal\block_content\BlockContentInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\layout_builder\LayoutEntityHelperTrait;
use Drush\Attributes as CLI;
use Drush\Commands\DrushCommands;
use Drush\Exceptions\UserAbortException;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * A Drush commandfile.
 */
class YmcaLbHeroBannerCommands extends DrushCommands {

  use LayoutEntityHelperTrait;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Constructs a YmcaLbHeroBannerCommands object.
   */
  public function __construct(EntityTypeManagerInterface $entityTypeManager) {
    parent::__construct();

    $this->entityTypeManager = $entityTypeManager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager'),
    );
  }

  /**
   * Update the entity node LB if it contains a Hero Banner block.
   */
  #[CLI\Command(name: 'ymca_lb_hero_banner:update-lb-hero-banner', aliases: ['lb_update_hero'])]
  #[CLI\Argument(name: 'content_type', description: 'Content type to update LB Hero block settings.')]
  #[CLI\Argument(name: 'view_mode', description: 'The view mode of the content type that contains LB Hero. Defaults to "default".')]
  #[CLI\Argument(name: 'h_element', description: 'H1 or H2 element to use in LB Hero. Defaults to "h2".')]
  #[CLI\Usage(name: 'ymca_lb_hero_banner:update-lb-hero-banner landing_page_lb', description: 'Update LB Hero settings for all Landing Page LB pages.')]
  #[CLI\Usage(name: 'ymca_lb_hero_banner:update-lb-hero-banner landing_page_lb default', description: 'Update LB Hero settings for all Landing Page LB pages. Location content types use "full" view mode.')]
  #[CLI\Usage(name: 'ymca_lb_hero_banner:update-lb-hero-banner landing_page_lb default h2', description: 'Update LB Hero settings for all Landing Page LB pages. Location content types use "full" view mode. H tag element is H2')]

  public function updateHeroBannerElement($content_type, $view_mode = 'default', $h_element = 'h2'): void {
    if (!$content_type) {
      return;
    }

    if (($h_element != 'h1') && ($h_element != 'h2')) {
      $this->io()->writeln('The ' . $h_element . ' is not allowed. Only h1 or h2 elements are supported.');
      return;
    }

    $really = $this->io()->confirm('Are you sure you want to update the layout builder Hero Banner block settings for ' . $content_type . ' nodes to use the ' . $h_element . ' tags?');

    if (!$really) {
      throw new UserAbortException("Command cancelled.");
    }

    // Query all content_type nodes.
    $nids = $this->entityTypeManager->getStorage('node')
      ->getQuery()
      ->condition('type', $content_type)
      ->accessCheck(FALSE)
      ->execute();

    if (empty($nids)) {
      $this->io()->writeln('No ' . $content_type . ' nodes found.');
      return;
    }

    // Initialize counters.
    $total_nodes = 0;
    $updated_nodes = 0;

    try {
      // Load and update each node LB Hero settings.
      $chunks = array_chunk($nids, 20);
      foreach ($chunks as $chunk) {
        $nodes = $this->entityTypeManager->getStorage('node')->loadMultiple($chunk);
        foreach ($nodes as $node) {
          $total_nodes++;
          $section_storage = $this->getSectionStorageForEntity($node);
          $sections = $section_storage->getSections();
          $first_lb_hero_updated = FALSE;
          if (!empty($sections)) {
            // Check each section in node and update LB Hero settings.
            foreach ($sections as $section) {
              if ($first_lb_hero_updated === TRUE) {
                break;
              }
              /** @var \Drupal\layout_builder\Section $section */
              $components = $section->getComponents();
              if (!empty($components)) {
                foreach ($components as $inline_block) {
                  if ($first_lb_hero_updated === TRUE) {
                    // Handle case if we have more than
                    // one Hero Banner block in the same section.
                    break 2;
                  }
                  $configuration = $inline_block->get('configuration');
                  if (isset($configuration['id']) &&
                    ($configuration['id'] == 'inline_block:lb_hero')) {
                    $hero_block =
                      $this->entityTypeManager->getStorage('block_content')->loadRevision(
                        $configuration['block_revision_id'],
                      );
                    if (($hero_block instanceof BlockContentInterface) &&
                      ($hero_block->hasField('field_heading_level'))) {
                      $field_heading_level = $hero_block->get('field_heading_level')->value;
                      if ($field_heading_level != $h_element) {
                        $hero_block->set('field_heading_level', $h_element);
                        $hero_block->setNewRevision();
                        $hero_block->save();
                        $configuration['block_revision_id'] = $hero_block->getRevisionId();
                        $inline_block->setConfiguration($configuration);
                        $first_lb_hero_updated = TRUE;
                      }
                      else {
                        // If first Hero Banner has the same H element value
                        // as the one we want to update, we simply
                        // consider it updated.
                        $first_lb_hero_updated = TRUE;
                      }
                    }
                  }
                }
              }
            }
            $node->layout_builder__layout->setValue($sections);
            $node->save();
            $updated_nodes++;
          }
        }
      }

    }
    catch (\Exception $exception) {
      \Drupal::logger('y_lb')->notice($exception->getMessage());
    }

    $this->io()->writeln($content_type . ' update - Found nodes: ' . $total_nodes . ', Updated nodes: ' . $updated_nodes);
  }

}
