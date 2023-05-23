<?php

namespace Drupal\y_lb\EventSubscriber;

use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\layout_builder\Event\PrepareLayoutEvent;
use Drupal\layout_builder\LayoutBuilderEvents;
use Drupal\layout_builder\LayoutTempstoreRepositoryInterface;
use Drupal\layout_builder\OverridesSectionStorageInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * An event subscriber to prepare section storage.
 *
 * Section storage works via the
 * \Drupal\layout_builder\Event\PrepareLayoutEvent.
 *
 * @see \Drupal\layout_builder\Event\PrepareLayoutEvent
 * @see \Drupal\layout_builder\Element\LayoutBuilder::prepareLayout()
 */
class PrepareLayout implements EventSubscriberInterface {

  use StringTranslationTrait;

  /**
   * The layout tempstore repository.
   *
   * @var \Drupal\layout_builder\LayoutTempstoreRepositoryInterface
   */
  protected $layoutTempstoreRepository;

  /**
   * The messenger service.
   *
   * @var \Drupal\Core\Messenger\MessengerInterface
   */
  protected $messenger;

  /**
   * The request stack.
   *
   * @var \Symfony\Component\HttpFoundation\RequestStack
   */
  protected $requestStack;


  /**
   * Constructs a new PrepareLayout.
   *
   * @param \Drupal\layout_builder\LayoutTempstoreRepositoryInterface $layout_tempstore_repository
   *   The tempstore repository.
   * @param \Drupal\Core\Messenger\MessengerInterface $messenger
   *   The messenger service.
   * @param \Symfony\Component\HttpFoundation\RequestStack $request_stack
   *   The request stack.
   */
  public function __construct(LayoutTempstoreRepositoryInterface $layout_tempstore_repository, MessengerInterface $messenger, RequestStack $request_stack) {
    $this->layoutTempstoreRepository = $layout_tempstore_repository;
    $this->messenger = $messenger;
    $this->requestStack = $request_stack;
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents(): array {
    $events[LayoutBuilderEvents::PREPARE_LAYOUT][] = ['onPrepareLayout', 10];
    return $events;
  }

  /**
   * Prepares a layout for use in the UI.
   *
   * @param \Drupal\layout_builder\Event\PrepareLayoutEvent $event
   *   The prepare layout event.
   */
  public function onPrepareLayout(PrepareLayoutEvent $event) {
    $section_storage = $event->getSectionStorage();

    $request = $this->requestStack->getCurrentRequest();

    if (!$request) {
      return;
    }
    // If the layout has pending changes, add a warning.
    if ($this->layoutTempstoreRepository->has($section_storage)) {

      // Add warning message after loaded page or triggered submit button.
      $triggeringElementName = $request->get('_triggering_element_name');
      if (empty($triggeringElementName) || $triggeringElementName === 'op') {
        $this->messenger->addWarning($this->t('You have unsaved changes.'));
      }
    }
    else {
      // If the layout is an override that has not yet been overridden, copy the
      // sections from the corresponding default.
      if ($section_storage instanceof OverridesSectionStorageInterface && !$section_storage->isOverridden()) {
        $sections = $section_storage->getDefaultSectionStorage()->getSections();
        foreach ($sections as $section) {
          $section_storage->appendSection($section);
        }
      }
      // Add storage to tempstore regardless of what the storage is.
      $this->layoutTempstoreRepository->set($section_storage);
    }
  }

}
