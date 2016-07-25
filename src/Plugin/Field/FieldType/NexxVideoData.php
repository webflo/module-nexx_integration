<?php

namespace Drupal\nexx_integration\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\TypedData\DataDefinition;

/**
 * Plugin implementation of the 'nexx_video_data' field type.
 *
 * @FieldType(
 *   id = "nexx_video_data",
 *   label = @Translation("Nexx Video data"),
 *   description = @Translation("Stores data as given by nexxOMNIA ping servic when a video has been created or modified."),
 *   default_formatter = "nexx_video_player",
 *   default_widget = "nexx_video_info",
 * )
 */
class NexxVideoData extends FieldItemBase {

  /**
   * {@inheritdoc}
   */
  public static function schema(
    FieldStorageDefinitionInterface $field_definition
  ) {
    return array(
      'columns' => array(
        'item_id' => array(
          'type' => 'int',
        ),
        'title' => array(
          'type' => 'varchar',
          'length' => 256,
        ),
        'subtitle' => array(
          'type' => 'varchar',
          'length' => 256,
        ),
        'alttitle' => array(
          'type' => 'varchar',
          'length' => 256,
        ),
        'teaser' => array(
          'type' => 'varchar',
          'length' => 256,
        ),
        'description' => array(
          'type' => 'varchar',
          'length' => 256,
        ),
        'altdescription' => array(
          'type' => 'varchar',
          'length' => 256,
        ),
        'uploaded' => array(
          'type' => 'int',
        ),
        'channel_id' => array(
          'type' => 'int',
        ),
        'actors_ids' => array(
          'type' => 'varchar',
          'length' => 256,
        ),
        'tags_ids' => array(
          'type' => 'varchar',
          'length' => 256,
        ),
        'isSSC' => array(
          'type' => 'int',
          'size' => 'tiny',
          'unsigned' => 'true',
        ),
        'encodedSSC' => array(
          'type' => 'int',
          'size' => 'tiny',
          'unsigned' => 'true',
        ),
        'validfrom_ssc' => array(
          'type' => 'int',
        ),
        'validto_ssc' => array(
          'type' => 'int',
        ),
        'encodedHTML5' => array(
          'type' => 'int',
          'size' => 'tiny',
          'unsigned' => 'true',
        ),
        'isMOBILE' => array(
          'type' => 'int',
          'size' => 'tiny',
          'unsigned' => 'true',
        ),
        'encodedMOBILE' => array(
          'type' => 'int',
          'size' => 'tiny',
          'unsigned' => 'true',
        ),
        'validfrom_mobile' => array(
          'type' => 'int',
        ),
        'validto_mobile' => array(
          'type' => 'int',
        ),
        'isHYVE' => array(
          'type' => 'int',
          'size' => 'tiny',
          'unsigned' => 'true',
        ),
        'encodedHYVE' => array(
          'type' => 'int',
          'size' => 'tiny',
          'unsigned' => 'true',
        ),
        'validfrom_hyve' => array(
          'type' => 'int',
        ),
        'validto_hyve' => array(
          'type' => 'int',
        ),
        'active' => array(
          'type' => 'int',
          'size' => 'tiny',
          'unsigned' => 'true',
        ),
        'isDeleted' => array(
          'type' => 'int',
          'size' => 'tiny',
          'unsigned' => 'true',
        ),
        'isBlocked' => array(
          'type' => 'int',
          'size' => 'tiny',
          'unsigned' => 'true',
        ),
        'encodedTHUMBS' => array(
          'type' => 'int',
          'size' => 'tiny',
          'unsigned' => 'true',
        ),
        'thumb' => array(
          'type' => 'varchar',
          'length' => 256,
        ),
        'copyright' => array(
          'type' => 'varchar',
          'length' => 256,
        ),
        'runtime' => array(
          'type' => 'varchar',
          'length' => 8,
        ),
      ),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function isEmpty() {
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(
    FieldStorageDefinitionInterface $field_definition
  ) {
    $properties['item_id'] = DataDefinition::create('integer')
      ->setLabel(t('Nexx item ID'));
    $properties['title'] = DataDefinition::create('string')
      ->setLabel(t('Title'));
    $properties['thumb'] = DataDefinition::create('string')
      ->setLabel(t('Thumbnail URL'));
    $properties['subtitle'] = DataDefinition::create('string')
      ->setLabel(t('Subtitle'));
    $properties['alttitle'] = DataDefinition::create('string')
      ->setLabel(t('Alternative title'));
    $properties['teaser'] = DataDefinition::create('string')
      ->setLabel(t('Teaser'));
    $properties['description'] = DataDefinition::create('string')
      ->setLabel(t('Description'));
    $properties['altdescription'] = DataDefinition::create('string')
      ->setLabel(t('Alternative description'));
    $properties['uploaded'] = DataDefinition::create('timestamp')
      ->setLabel(t('Time of upload'));
    $properties['channel_id'] = DataDefinition::create('integer')
      ->setLabel(t('Channel ID'));
    $properties['actors_ids'] = DataDefinition::create('string')
      ->setLabel(t('Actor IDs'));
    $properties['tags_ids'] = DataDefinition::create('string')
      ->setLabel(t('Tag IDs'));
    $properties['isSSC'] = DataDefinition::create('boolean')
      ->setLabel(t('isSSC'));
    $properties['encodedSSC'] = DataDefinition::create('boolean')
      ->setLabel(t('encoded SSC'));
    $properties['validfrom_ssc'] = DataDefinition::create('timestamp')
      ->setLabel(t('Valid from SSC'));
    $properties['validto_ssc'] = DataDefinition::create('timestamp')
      ->setLabel(t('Valid to SSC'));
    $properties['encodedHTML5'] = DataDefinition::create('boolean')
      ->setLabel(t('encoded HTML5'));
    $properties['isMOBILE'] = DataDefinition::create('boolean')
      ->setLabel(t('Is mobile'));
    $properties['encodedMOBILE'] = DataDefinition::create('boolean')
      ->setLabel(t('Encoded mobile'));
    $properties['validfrom_mobile'] = DataDefinition::create('timestamp')
      ->setLabel(t('Valid from mobile'));
    $properties['validto_mobile'] = DataDefinition::create('timestamp')
      ->setLabel(t('Valid to mobile'));
    $properties['isHYVE'] = DataDefinition::create('boolean')
      ->setLabel(t('is HYVE'));
    $properties['encodedHYVE'] = DataDefinition::create('boolean')
      ->setLabel(t('Encoded HYVE'));
    $properties['validfrom_hyve'] = DataDefinition::create('timestamp')
      ->setLabel(t('Valid from HYVE'));
    $properties['validto_hyve'] = DataDefinition::create('timestamp')
      ->setLabel(t('Valid to HYVE'));
    $properties['active'] = DataDefinition::create('boolean')
      ->setLabel(t('Is active'));
    $properties['isDeleted'] = DataDefinition::create('boolean')
      ->setLabel(t('Is deleted'));
    $properties['isBlocked'] = DataDefinition::create('boolean')
      ->setLabel(t('Is blocked'));
    $properties['encodedTHUMBS'] = DataDefinition::create('boolean')
      ->setLabel(t('Encoded thumbs'));
    $properties['copyright'] = DataDefinition::create('string')
      ->setLabel(t('Copyright'));
    $properties['runtime'] = DataDefinition::create('string')
      ->setLabel(t('Runtime'));
    return $properties;
  }

}
