<?php

/**
 * @file
 * Contains \Drupal\nexx_integration\Plugin\MediaEntity\Type\NexxVideo.
 */

namespace Drupal\nexx_integration\Plugin\MediaEntity\Type;

use Drupal\Core\Config\Config;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Entity\EntityManager;
use Drupal\media_entity\MediaBundleInterface;
use Drupal\media_entity\MediaInterface;
use Drupal\media_entity\MediaTypeBase;
use Symfony\Component\DependencyInjection\ContainerInterface;


/**
 * Provides media type plugin for Image.
 *
 * @MediaType(
 *   id = "nexx_video",
 *   label = @Translation("Nexx video"),
 *   description = @Translation("Handles videos from nexxOmnia Video CMS.")
 * )
 */
class NexxVideo extends MediaTypeBase {
  /**
   * {@inheritdoc}
   */
  public function providedFields() {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function getField(MediaInterface$media, $name) {
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function thumbnail(MediaInterface $media) {
    $source_field = $this->configuration['source_field'];

    /** @var \Drupal\file\FileInterface $file */
    $file = $this->entityTypeManager->getStorage('file')->load($media->{$source_field}->target_id);

    if (!$file) {
      return $this->config->get('icon_base') . '/nexxvideo.png';
    }

    return $file->getFileUri();
  }
}
