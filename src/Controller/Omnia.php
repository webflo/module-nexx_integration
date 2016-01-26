<?php

namespace Drupal\nexx_integration\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeBundleInfoInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class Omnia extends ControllerBase {
  /**
   * @var \Drupal\Core\Entity\EntityTypeBundleInfo
   */
  protected $entityTypeBundleInfo;

  /**
   *
   */
  protected $mediaEntity;

  /**
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $mediaEntityStorage;

  /**
   * @var \Drupal\Core\Entity\EntityTypeInterface
   */
  protected $mediaEntityDefinition;

  /**
   * @var \Drupal\Core\Entity\EntityFieldManagerInterface
   */
  protected $entityFieldManager;

  /**
   * @var \Psr\Log\LoggerInterface
   */
  protected $logger;

  /**
   *
   * @param EntityTypeBundleInfoInterface $entity_type_bundle_info
   *  The date formatter service.
   * @param EntityFieldManagerInterface $entity_field_manager
   *  The entity field manager
   * @param LoggerInterface $logger
   *  The logger service
   */
  public function __construct(
    EntityTypeBundleInfoInterface $entity_type_bundle_info,
    EntityFieldManagerInterface $entity_field_manager,
    LoggerInterface $logger
  ) {
    $this->entityTypeBundleInfo = $entity_type_bundle_info;
    $this->entityFieldManager = $entity_field_manager;
    $this->logger = $logger;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.bundle.info'),
      $container->get('entity_field.manager'),
      $container->get('logger.factory')->get('nexx_integration')
    );
  }

  /**
   * Endpoint for video creation / update
   */
  public function video(Request $request) {
    $response = new JsonResponse();
    $content = $request->getContent();
    $query = $this->mediaEntityStorage()->getQuery();

    if (!empty($content)) {
      $videoData = json_decode($content);
    }

    if (!isset($videoData->itemID)) {
      throw new \Exception('ItemID missing');
    }

    $this->logger->info("@content", array('@content' => $content));
    $this->logger->info('Incoming video "@title" (nexx id: @id)', array('@title' => $videoData->itemData->title, '@id' => $videoData->itemID));

    $video_field = $this->videoFieldName();
    $ids = $query->condition($video_field . '.item_id', $videoData->itemID)
      ->execute();

    if ($id = array_pop($ids)) {
      $media = $this->mediaEntity($id);
    }
    else {
      $media = $this->mediaEntity();
    }
    $this->mapData($media, $videoData);
    $media->save();
    $this->logger->info('Updated video "@title" (drupal id: @id)', array('@title' => $videoData->itemData->title, '@id' => $media->id()));
    $response->setdata(['refnr' => $videoData->itemID, 'value' => $media->id()]);
    return $response;
  }


  protected function mediaEntity($id = NULL) {
    if (!isset($this->mediaEntity)) {
      $storage = $this->mediaEntityStorage();
      if ($id) {
        $this->mediaEntity = $storage->load($id);
      }
      else {
        $videoBundle = $this->config('nexx_integration.settings')
          ->get('video_bundle');
        $this->mediaEntity = $storage->create(['bundle' => $videoBundle]);
      }
    }
    return $this->mediaEntity;
  }

  protected function mapData(EntityInterface $media, $videoData) {
    $entityType = $this->mediaEntityDefinition();
    $channel_taxonomy = $this->channelTaxonomy();
    $actor_taxonomy = $this->actorTaxonomy();

    $channelField = $this->taxonomyFieldName($channel_taxonomy, $media);
    $actorField = $this->taxonomyFieldName($actor_taxonomy, $media);

    $videoField = $this->videoFieldName();
    $labelKey = $entityType->getKey('label');

    $media->$videoField->item_id = !empty($videoData->itemID) ? $videoData->itemID : 0;
    $media->$videoField->title = !empty($videoData->itemData->title) ? $videoData->itemData->title : '';
    $media->$videoField->alttitle = !empty($videoData->itemData->alttitle) ? $videoData->itemData->alttitle : '';
    $media->$videoField->subtitle = !empty($videoData->itemData->subtitle) ? $videoData->itemData->subtitle : '';
    $media->$videoField->teaser = !empty($videoData->itemData->teaser) ? $videoData->itemData->teaser : '';
    $media->$videoField->description = !empty($videoData->itemData->description) ? $videoData->itemData->description : '';
    $media->$videoField->altdescription = !empty($videoData->itemData->altdescription) ? $videoData->itemData->altdescription : '';
    $media->$videoField->uploaded = !empty($videoData->itemData->uploaded) ? $videoData->itemData->uploaded : '';
    $media->$videoField->channel_id = !empty($videoData->itemData->channel_id) ? $videoData->itemData->channel_id : 0;
    $media->$videoField->actors_ids = !empty($videoData->itemData->actors_ids) ? $videoData->itemData->actors_ids : '';
    $media->$videoField->isSSC = !empty($videoData->itemStates->isSSC) ? $videoData->itemStates->isSSC : 0;
    $media->$videoField->encodedSSC = !empty($videoData->itemStates->encodedSSC) ? $videoData->itemStates->encodedSSC : 0;
    $media->$videoField->validfrom_ssc = !empty($videoData->itemStates->validfrom_ssc) ? $videoData->itemStates->validfrom_ssc : 0;
    $media->$videoField->validto_ssc = !empty($videoData->itemStates->validto_ssc) ? $videoData->itemStates->validto_ssc : 0;
    $media->$videoField->encodedHTML5 = !empty($videoData->itemStates->encodedHTML5) ? $videoData->itemStates->encodedHTML5 : 0;
    $media->$videoField->isMOBILE = !empty($videoData->itemStates->isMOBILE) ? $videoData->itemStates->isMOBILE : 0;
    $media->$videoField->encodedMOBILE = !empty($videoData->itemStates->encodedMOBILE) ? $videoData->itemStates->encodedMOBILE : 0;
    $media->$videoField->validfrom_mobile = !empty($videoData->itemStates->validfrom_mobile) ? $videoData->itemStates->validfrom_mobile : 0;
    $media->$videoField->validto_mobile = !empty($videoData->itemStates->validto_mobile) ? $videoData->itemStates->validto_mobile : 0;
    $media->$videoField->isHYVE = !empty($videoData->itemStates->isHYVE) ? $videoData->itemStates->isHYVE : 0;
    $media->$videoField->encodedHYVE = !empty($videoData->itemStates->encodedHYVE) ? $videoData->itemStates->encodedHYVE : 0;
    $media->$videoField->validfrom_hyve = !empty($videoData->itemStates->validfrom_hyve) ? $videoData->itemStates->validfrom_hyve : 0;
    $media->$videoField->validto_hyve = !empty($videoData->itemStates->validto_hyve) ? $videoData->itemStates->validto_hyve : 0;
    $media->$videoField->active = !empty($videoData->itemStates->active) ? $videoData->itemStates->active : 0;
    $media->$videoField->isDeleted = !empty($videoData->itemStates->isDeleted) ? $videoData->itemStates->isDeleted : 0;
    $media->$videoField->isBlocked = !empty($videoData->itemStates->isBlocked) ? $videoData->itemStates->isBlocked : 0;
    $media->$videoField->encodedTHUMBS = !empty($videoData->itemStates->encodedTHUMBS) ? $videoData->itemStates->encodedTHUMBS : 0;

    // copy title to label field
    $media->$labelKey = $media->$videoField->title;

    // update taxonomy references
    if ($channelField && !empty($media->$videoField->channel_id)) {
      $media->$channelField = $media->$videoField->channel_id;
    }
    if ($actorField && !empty($media->$videoField->actors_ids)) {
      $media->$actorField = explode(',', $media->$videoField->actors_ids);
    }
  }

  /**
   * Retrieves configured channel taxonomy
   */
  protected function channelTaxonomy() {
    return $this->config('nexx_integration.settings')
      ->get('channel_vocabulary');
  }

  /**
   * Retrieves configured actor taxonomy
   */
  protected function actorTaxonomy() {
    return $this->config('nexx_integration.settings')->get('actor_vocabulary');
  }

  /**
   * Retrieves the media entity storage.
   *
   * @return \Drupal\Core\Entity\EntityStorageInterface
   *   The entity type manager.
   */
  protected function mediaEntityStorage() {
    if (!isset($this->mediaEntityStorage)) {
      $this->mediaEntityStorage = $this->entityTypeManager()
        ->getStorage('media');
    }
    return $this->mediaEntityStorage;
  }

  /**
   * Retrieves the media entity definition.
   *
   * @return \Drupal\Core\Entity\EntityTypeInterface
   *   The entity type manager.
   */
  protected function mediaEntityDefinition() {
    if (!isset($this->mediaEntityDefinition)) {
      $this->mediaEntityDefinition = $this->entityTypeManager()
        ->getDefinition('media');
    }
    return $this->mediaEntityDefinition;
  }

  protected function videoFieldName() {
    $entity_type_id = 'media';
    $videoBundle = $this->config('nexx_integration.settings')
      ->get('video_bundle');

    $fieldDefinitions = $this->entityFieldManager->getFieldDefinitions($entity_type_id, $videoBundle);
    foreach ($fieldDefinitions as $fieldname => $fieldDefinition) {
      if ($fieldDefinition->getType() === 'nexx_video_data') {
        $videoField = $fieldname;
        break;
      }
    }

    if (empty($videoField)) {
      throw new \Exception('No video data field defined');
    }

    return $videoField;
  }

  protected function taxonomyFieldName($target_bundle, EntityInterface $media) {
    $fieldDefinitions = $this->entityFieldManager->getFieldDefinitions($media->getEntityType()
      ->id(), $media->bundle()
    );

    foreach ($fieldDefinitions as $currentFieldName => $fieldDefinition) {
      // find taxonomy term, reference for given bundle
      if ($fieldDefinition->getType() === 'entity_reference' && $fieldDefinition->getFieldStorageDefinition()
          ->getSetting('target_type') === 'taxonomy_term' && !empty($fieldDefinition->getSetting('handler_settings')['target_bundles'][$target_bundle])
      ) {
        $fieldName = $currentFieldName;
        break;
      }
    }
    if (empty($fieldName)) {
      throw new \Exception("No $target_bundle referencing field found");
    }

    return $fieldName;
  }


  protected function termId($taxonomy, $name) {
    return array_shift($this->entityTypeManager()
      ->getStorage('taxonomy_term')
      ->loadByProperties([
          'vid' => $taxonomy,
          'name' => trim($name)
        ]
      )
    );
  }
}
