<?php

/**
 * @file
 * Contains \Drupal\nexx_integration\Plugin\MediaEntity\Type\NexxVideo.
 */

namespace Drupal\nexx_integration\Plugin\MediaEntity\Type;

use Drupal\Core\Form\FormStateInterface;
use Drupal\media_entity\MediaBundleInterface;
use Drupal\media_entity\MediaInterface;
use Drupal\media_entity\MediaTypeBase;


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
  public function getField(MediaInterface $media, $name) {
    return;
  }

  /**
   * {@inheritdoc}
   */
  public function thumbnail(MediaInterface $media) {
    // TODO: implement logic to fill this with the provided thumbnail
    return $this->config->get('icon_base') . '/nexxvideo.png';
  }
  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    /** @var MediaBundleInterface $bundle */
    $bundle = $form_state->getFormObject()->getEntity();

    $default_bundle = !empty($values['channel_field']) ? $values['channel_field'] : $this->configuration['channel_field'];
    $form['channel_field'] = [
      '#type' => 'select',
      '#title' => 'Channel ' . $bundle->label() ?: $this->t('Fields'),
      '#options' => $this->getMediaEntityReferenceFields($bundle->id(), ['taxonomy_term']),
      '#empty_option' => $this->t('Select field'),
      '#default_value' => $default_bundle,
      '#description' => $this->t('The taxonomy which is used for videos. You can create a bundle without selecting a value for this dropdown initially. This dropdown can be populated after adding taxonomy term entity references to the bundle.'),
    ];

    $default_bundle = !empty($values['actor_field']) ? $values['actor_field'] : $this->configuration['actor_field'];
    $form['actor_field'] = [
      '#type' => 'select',
      '#title' => 'Actor ' . $bundle->label() ?: $this->t('Fields'),
      '#options' => $this->getMediaEntityReferenceFields($bundle->id(), ['taxonomy_term']),
      '#empty_option' => $this->t('Select field'),
      '#default_value' => $default_bundle,
      '#description' => $this->t('The taxonomy which is used for actors. You can create a bundle without selecting a value for this dropdown initially. This dropdown can be populated after adding taxonomy term entity references to the bundle.'),
    ];

    $default_bundle = !empty($values['teaser_image_field']) ? $values['teaser_image_field'] : $this->configuration['teaser_image_field'];
    $form['teaser_image_field'] = [
      '#type' => 'select',
      '#title' => 'Teaser image ' . $bundle->label() ?: $this->t('Fields'),
      '#options' => $this->getMediaEntityReferenceFields($bundle->id(), ['media', 'image', 'file']),
      '#empty_option' => $this->t('Select field'),
      '#default_value' => $default_bundle,
      '#description' => $this->t('The taxonomy which is used for actors. You can create a bundle without selecting a value for this dropdown initially. This dropdown can be populated after adding taxonomy term entity references to the bundle.'),
    ];
    return $form;
  }

  /**
   * Builds a list of references for a media entity.
   *
   * @param $bundle_id Entity type to get references for
   * @param $target_types Target types filter
   * @return array
   *   An array of field labels, keyed by field name.
   */
  protected function getMediaEntityReferenceFields($bundle_id, array $target_types) {
    $bundle_options = array();

    foreach ($this->entityFieldManager->getFieldDefinitions('media', $bundle_id) as $field_id => $field_info) {
      // filter entity_references which are not base fields
      if ($field_info->getType() === 'entity_reference' && !$field_info->getFieldStorageDefinition()->isBaseField() && in_array($field_info->getSettings()['target_type'], $target_types)) {
        $bundle_options[$field_id] = $field_info->getLabel();
      }
    }
    natsort($bundle_options);
    return $bundle_options;
  }
}
