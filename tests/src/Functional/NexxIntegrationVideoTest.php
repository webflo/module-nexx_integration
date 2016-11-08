<?php

namespace Drupal\Tests\nexx_integration\Functional;

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
    $videoData = $this->postVideoData();
    $this->assertEquals('75045', $videoData->refnr);
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
   * @param array $data
   *   Json encoded data to send at video endpoint.
   *
   * @return mixed
   *   Response Body of request.
   */
  protected function postVideoData($data = []) {
    if (empty($data)) {
      $data = $this->getTestVideoData();
    }
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
    print_r($responseBody);
    return $responseBody;
  }

  /**
   * Display the video with the given ID.
   *
   * @param int $videoId
   *   Video ID.
   */
  protected function displayVideo($videoId) {

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
    $itemData->subtitle = "";
    $itemData->teaser = "";
    $itemData->orderhint = "";
    $itemData->description = "The description text";
    $itemData->category = "";
    $itemData->channel = "CMS";
    $itemData->videotype = "movie";
    $itemData->genre = "";
    $itemData->isPay = "0";
    $itemData->uploaded = 1463997938;
    $itemData->tags = "Tag1,Tag2";
    $itemData->lat = "0";
    $itemData->lng = "0";
    $itemData->location = "";
    $itemData->country = "";
    $itemData->actors = "";
    $itemData->shows = "";
    $itemData->voices = "";
    $itemData->director = "";
    $itemData->producer = "";
    $itemData->cameraman = "";
    $itemData->scriptby = "";
    $itemData->musicby = "";
    $itemData->conductor = "";
    $itemData->studio = "0";
    $itemData->year = "2016";
    $itemData->copyright = "";
    $itemData->imagecopyright = "";
    $itemData->awards = "";
    $itemData->ages = "0";
    $itemData->hasTrailerID = "0";
    $itemData->isReferenceOf = "0";
    $itemData->linkedAlbum = "0";
    $itemData->linkedFile = 0;
    $itemData->language = "deutsch";
    $itemData->encodedTHUMBS = "1";
    $itemData->rating = 3;
    $itemData->ratingcount = 0;
    $itemData->thumb = "http://nx-i.akamaized.net/201605/G750452J1M6XAOWxL.jpg";
    $itemData->thumb_ssl = "https://nx-i.akamaized.net/201605/G750452J1M6XAOWxL.jpg";
    $itemData->thumb_alt = "http://nx-i.akamaized.net/global/nodata/nodataxL.jpg";
    $itemData->thumb_alt_ssl = "https://nx-i.akamaized.net/global/nodata/nodataxL.jpg";
    $itemData->thumb_action = "http://nx-i.akamaized.net/global/nodata/nodataxL.jpg";
    $itemData->thumb_action_ssl = "https://nx-i.akamaized.net/global/nodata/nodataxL.jpg";
    $itemData->runtime = "00:02:45";
    $itemData->hasSubtitles = 0;
    $itemData->userid = 0;
    $itemData->orientation = "landscape";
    $itemData->isTrailerOf = 0;
    $itemData->thumb_animatedgif = "http://nx-i.akamaized.net/global/nodata/nodata.jpg";
    $itemData->thumb_animatedgif_ssl = "https://nx-i.akamaized.net/global/nodata/nodata.jpg";
    $itemData->thumb_hasX2 = "0";
    $itemData->thumb_hasX3 = "0";
    $itemData->categoryname = "";
    $itemData->channel_id = "1";
    $itemData->parent_channel = 0;
    $itemData->commentcount = 0;
    $itemData->actors_ids = "1,2";
    $itemData->likecount = 0;
    $itemData->tags_ids = "2";
    $itemData->studioname = "";
    $itemData->currency = "EUR";
    $itemData->discount = 0;
    $itemData->price = 0;
    $itemData->originalprice = 0;
    $itemData->genre_ids = "";

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
    $itemStates->autodelete = "0000-00-00";
    $itemStates->georestriction = "";
    $itemStates->geoexcludes = "";
    $itemStates->ages = 0;

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
