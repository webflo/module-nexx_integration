<?php

namespace Drupal\Tests\nexx_integration\Unit;

use Drupal\Tests\UnitTestCase;
use Drupal\nexx_integration\Controller\OmniaController;

/**
 * Provides automated tests for the nexx integration Omnia controller.
 *
 * @group nexx_integration
 */
class OmniaControllerTest extends UnitTestCase {

  /**
   * {@inheritdoc}
   */
  public static function getInfo() {
    return array(
      'name' => "OmniaController functionality",
      'description' => 'Test Unit for module nexx_integration\'s  omnia controller.',
    );
  }

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();
  }

}
