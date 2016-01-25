<?php

/**
 * @file
 * Contains Drupal\nexx_integration\NexxNotification
 */

namespace Drupal\nexx_integration;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\Query\QueryFactory;

class NexxNotification implements NexxNotificationInterface {
  /**
   * Generates IVW tracking information.
   *
   * @param EntityTypeManagerInterface $entity_type_manager
   *   The entity query object for taxonomy terms.
   * @param QueryFactory $query
   *   The entity query object for taxonomy terms.
   * @param ConfigFactoryInterface $config_factory
   *   The config factory service.
   */
  public function __construct(
    EntityTypeManagerInterface $entity_type_manager,
    QueryFactory $query,
    ConfigFactoryInterface $config_factory
  ) {
    $this->termStorage = $entity_type_manager->getStorage('taxonomy_term');
    $this->nodeQuery = $query->get('node');
    $this->configFactory = $config_factory;
  }
}

