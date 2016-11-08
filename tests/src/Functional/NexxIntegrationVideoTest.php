<?php

namespace Drupal\Tests\nexx_integration\Functional;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Url;
use Drupal\Tests\BrowserTestBase;

/**
 * Test infinite_base admin interface.
 *
 * @group nexx_integration
 */
class NexxIntegrationVideoTest extends BrowserTestBase {

  protected $config;

  protected $adminUser;

  protected $videoUser;

  public static $modules = [
    'nexx_integration',
  ];

  /**
   * Setup the tests.
   */
  protected function setUp() {
    parent::setUp();
    $this->adminUser = $this->drupalCreateUser(['administer site configuration']);
    $this->videoUser = $this->drupalCreateUser(['use omnia notification gateway']);
    $this->setTestConfig();
    $this->config = $this->config('nexx_integration.settings');
  }

  /**
   * Test the endpoint.
   */
  public function testVideoEndpoint() {
    $data = $this->getTestVideoData();

    // Test connectivity.
    $videoData = $this->postVideoData($data);
    $this->assertEquals($data->itemID, $videoData->refnr);

    // Test created entity.
    $videoEntity = $this->loadVideoEntity($videoData->value);
    $videoField = $videoEntity->get('field_video');

    $this->assertEquals($data->itemData->itemID, $videoField->item_id);
    $this->assertEquals($data->itemData->title, $videoField->title);
    $this->assertEquals($data->itemData->hash, $videoField->hash);
    $this->assertEquals($data->itemData->teaser, $videoField->teaser);
    $this->assertEquals($data->itemData->description, $videoField->description);
    $this->assertEquals($data->itemData->uploaded, $videoField->uploaded);
    $this->assertEquals($data->itemData->copyright, $videoField->copyright);
    $this->assertEquals($data->itemData->encodedTHUMBS, $videoField->encodedTHUMBS);
    $this->assertEquals($data->itemData->runtime, $videoField->runtime);
  }

  /**
   * Configure nexx settings.
   */
  protected function setTestConfig() {
    $this->drupalLogin($this->adminUser);
    $this->drupalGet(Url::fromRoute('nexx_integration.admin_settings'));
    $page = $this->getSession()->getPage();
    $page->fillField('edit-omnia-id', '1');
    $page->fillField('edit-notification-access-key', 'test-access-key');
    $page->pressButton('edit-submit');
  }

  /**
   * Post video data to endpoint.
   *
   * If no data is given, test data will be send.
   *
   * @param object $data
   *   Data to send at video endpoint.
   *
   * @return mixed
   *   Response Body of request.
   */
  protected function postVideoData($data) {
    $omniaUrl = Url::fromRoute('nexx_integration.omnia_notification_gateway', ['token' => $this->config->get('notification_access_key')], ['absolute' => TRUE]);
    $httpClient = $this->container->get('http_client');

    /* @var $response \GuzzleHttp\Psr7\Response */
    $response = $httpClient->post($omniaUrl->toString(), [
      'body' => json_encode($data),
      'headers' => [
        'Content-Type' => 'application/json',
      ],
    ]);

    $responseBody = \GuzzleHttp\json_decode($response->getBody()->getContents());
    return $responseBody;
  }

  /**
   * Load a video media entity.
   *
   * @param int $videoId
   *   The entity ID.
   *
   * @return EntityInterface
   *   The vide entity.
   */
  protected function loadVideoEntity($videoId) {
    $entityTypeManager = $this->container->get('entity_type.manager');
    return $entityTypeManager->getStorage('media')->load($videoId);
  }

  /**
   * Create test data string.
   *
   * @return string
   *   Test data.
   */
  protected function getTestVideoData() {
    $itemData = new \stdClass();
    $itemData->itemID = "75045";
    $itemData->hash = "GL7ADZXZJ75045P";
    $itemData->connector = "612";
    $itemData->title = "Test Video";
    $itemData->teaser = "The teaser text.";
    $itemData->orderhint = "";
    $itemData->description = "The description text";
    $itemData->uploaded = 1463997938;
    $itemData->tags = "Tag1,Tag2";
    $itemData->copyright = "";
    $itemData->encodedTHUMBS = "1";
    $itemData->thumb = "http://nx-i.akamaized.net/201605/G750452J1M6XAOWxL.jpg";
    $itemData->runtime = "00:02:45";
    $itemData->channel_id = "1";
    $itemData->actors_ids = "1,2";
    $itemData->tags_ids = "3,4";

    $itemStates = new \stdClass();
    $itemStates->isSSC = 1;
    $itemStates->encodedSSC = 1;
    $itemStates->validfrom_ssc = 0;
    $itemStates->validto_ssc = 0;
    $itemStates->encodedHTML5 = 1;
    $itemStates->isMOBILE = 1;
    $itemStates->encodedMOBILE = 1;
    $itemStates->validfrom_mobile = 0;
    $itemStates->validto_mobile = 0;
    $itemStates->active = 1;
    $itemStates->isDeleted = 0;
    $itemStates->isBlocked = 0;
    $itemStates->encodedTHUMBS = 1;
    $itemStates->validto_image = 0;


    $baseData = new \stdClass();
    $baseData->itemID = "75045";
    $baseData->itemReference = "";
    $baseData->itemMime = "video";
    $baseData->clientID = "1";
    $baseData->triggerReason = "metadata";
    $baseData->triggerTime = "1465392767";
    $baseData->sendingTime = 1465392783;
    $baseData->triggeredInSession = "214653913620510632";
    $baseData->triggeredByUser = "119574";
    $baseData->itemData = $itemData;
    $baseData->itemStates = $itemStates;

    return $baseData;
  }

}
