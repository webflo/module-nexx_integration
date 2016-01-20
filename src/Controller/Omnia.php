<?php

namespace Drupal\nexx_integration\Controller;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeBundleInfoInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

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
   *
   * @param EntityTypeBundleInfoInterface $entity_type_bundle_info
   *    The date formatter service.
   * @param EntityFieldManagerInterface $entity_field_manager
   */
  public function __construct(EntityTypeBundleInfoInterface $entity_type_bundle_info, EntityFieldManagerInterface $entity_field_manager) {
    $this->entityTypeBundleInfo = $entity_type_bundle_info;
    $this->entityFieldManager = $entity_field_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.bundle.info'),
      $container->get('entity_field.manager')
    );
  }

  /**
   * Endpoint for video creation / update
   */
  public function video(Request $request) {
    $response = new AjaxResponse();
    $content = $request->getContent();
    $query = $this->mediaEntityStorage()->getQuery();

    if (!empty($content)) {
      $videoData = json_decode($content);
    }

    if (isset($videoData->itemID)) {
      $itemID = $videoData->itemID;
    }
    else {
      throw new \Exception('ItemID missing');
    }
    $video_field = $this->videoFieldName();
    $ids = $query->condition($video_field . '.item_id', $itemID)->execute();

    if ($id = array_pop($ids)) {
      $this->updateVideo($id, $videoData);
    }
    else {
      $this->createVideo($videoData);
    }

    $response->setContent();
    return $response;
  }

  protected function updateVideo($id, $videoData) {
    $media = $this->mediaEntity($id);
    $this->mapData($media, $videoData);
    $media->save();
  }

  protected function createVideo($id, $videoData) {

  }

  protected function mediaEntity($id) {
    if (!isset($this->mediaEntity)) {
      $storage = $this->mediaEntityStorage();
      $this->mediaEntity = $storage->load($id);
    }
    return $this->mediaEntity;
  }

  protected function mapData(EntityInterface $media, $videoData) {
    $entityType = $this->mediaEntityDefinition();
    $channel_taxonomy = $this->channelTaxonomy();
    $actor_taxonomy = $this->actorTaxonomy();

    $actors = [];
    foreach(explode(',', $videoData->itemData->actors_ids) as $actor) {
      $actors[] = $this->termId($actor_taxonomy, $actor);
    }
    $channel = $this->termId($channel_taxonomy, $videoData->itemData->channel);



    $videoField = $this->videoFieldName();
    $labelKey = $entityType->getKey('label');

    $media->$videoField->title = $videoData->itemData->title;
    $media->$videoField->alttitle = $videoData->itemData->alttitle;
    $media->$videoField->subtitle = $videoData->itemData->subtitle;
    $media->$videoField->teaser = $videoData->itemData->teaser;
    $media->$videoField->description = $videoData->itemData->description;
    $media->$videoField->altdescription = $videoData->itemData->altdescription;
    $media->$videoField->uploaded = $videoData->itemData->uploaded;
    $media->$videoField->channelid = $channel->id();
    $media->$videoField->actors_ids = implode(',', $actors);
    $media->$videoField->isSSC = $videoData->itemStates->isSSC;
    $media->$videoField->encodedSSC = $videoData->itemStates->encodedSSC;
    $media->$videoField->validfrom_ssc = $videoData->itemStates->validfrom_ssc;
    $media->$videoField->validto_ssc = $videoData->itemStates->validto_ssc;
    $media->$videoField->encodedHTML5 = $videoData->itemStates->encodedHTML5;
    $media->$videoField->isMOBILE = $videoData->itemStates->isMOBILE;
    $media->$videoField->encodedMOBILE = $videoData->itemStates->encodedMOBILE;
    $media->$videoField->validfrom_mobile = $videoData->itemStates->validfrom_mobile;
    $media->$videoField->validto_mobile = $videoData->itemStates->validto_mobile;
    $media->$videoField->isHYVE = $videoData->itemStates->isHYVE;
    $media->$videoField->encodedHYVE = $videoData->itemStates->encodedHYVE;
    $media->$videoField->validfrom_hyve = $videoData->itemStates->validfrom_hyve;
    $media->$videoField->validto_hyve = $videoData->itemStates->validto_hyve;
    $media->$videoField->active = $videoData->itemStates->active;
    $media->$videoField->isDeleted = $videoData->itemStates->isDeleted;
    $media->$videoField->isBlocked = $videoData->itemStates->isBlocked;
    $media->$videoField->encodedTHUMBS = $videoData->itemStates->encodedTHUMBS;

    $media->$labelKey = $media->$videoField->title;
  }

  /**
   * Retrieves configured channel taxonomy
   */
  protected function channelTaxonomy() {
    return $this->config('nexx_integration.settings')->get('channel_vocabulary');
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
    $mediaBundleInfo = $this->entityTypeBundleInfo->getBundleInfo($entity_type_id);

    foreach(array_keys($mediaBundleInfo) as $bundle){
      $fieldDefinitions = $this->entityFieldManager->getFieldDefinitions($entity_type_id, $bundle);
      foreach($fieldDefinitions as $fieldname => $fieldDefinition){
        if($fieldDefinition->getType() ===  'nexx_video_data') {
          $videoField = $fieldname;
          break 2;
        }
      }
    }


    if(empty($videoField)) {
      throw new \Exception('No video data field defined');
    }

    return $videoField;
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
