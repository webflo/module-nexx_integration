<?php

namespace Drupal\nexx_integration\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Ajax\AjaxResponse;

class Omnia extends ControllerBase {
  /**
   * Endpoint for video creation / update
   */
  public function video() {
    $response = new AjaxResponse();
    $response->setContent('OK');
    return $response;
  }
}
