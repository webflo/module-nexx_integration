<?php

namespace Drupal\nexx_integration\Controller;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityInterface;
use Symfony\Component\HttpFoundation\Request;

class Omnia extends ControllerBase {
  /**
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $mediaEntityStorage;

  /**
   * @var \Drupal\Core\Entity\EntityTypeInterface
   */
  protected $mediaEntityDefinition;

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

    $ids = $query->condition('field_video_data.item_id', $itemID)->execute();

    if ($id = array_pop($ids)) {
      $this->updateVideo($id, $videoData);
    }
    else {
      $this->createVideo($videoData);
    }

    $response->setContent();
    return $response;
  }

  private function updateVideo($id, $videoData) {
    $storage = $this->mediaEntityStorage();
    $media = $storage->load($id);

    $this->mapData($media, $videoData);
    $media->save();
  }

  private function createVideo($id, $videoData) {

  }

  private function mapData(EntityInterface $media, $videoData) {
    $entityType = $this->mediaEntityDefinition();

    $labelKey = $entityType->getKey('label');
    $media->$videoField->

    $media->$labelKey = $videoData->itemData->title;

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

  protected function videoFieldName($media_id) {
    foreach($media->getFieldDefinitions() as $fieldname => $fieldDefinition){
      if($fieldDefinition->getType() ===  'nexx_video_data') {
        $videoField = $fieldname;
        break;
      }
    }

    if(empty($videoField)) {
      throw new \Exception('No video data field defined');
    }
  }
}
