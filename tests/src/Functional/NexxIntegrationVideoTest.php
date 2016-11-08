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
      'body' => $data,
      'headers' => [
        'Content-Type' => 'application/json',
      ],
    ]);

    $responseBody = \GuzzleHttp\json_decode($response->getBody());
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

    // This is a data dump from an original call.
    $serializedData = '{
      "itemID": "75045",
      "itemReference": "",
      "itemMime": "video",
      "clientID": "612",
      "triggerReason": "metadata",
      "triggerTime": "1465392767",
      "sendingTime": 1465392783,
      "triggeredInSession": "214653913620510632",
      "triggeredByUser": "119574",
      "itemData": {
        "itemID": "75045",
        "hash": "GL7ADZXZJ75045P",
        "connector": "612",
        "title": "Thunder CMS token 2",
        "subtitle": "",
        "teaser": "",
        "orderhint": "",
        "description": "Lavendel ist eine sehr dankbare und pflegeleichte die Pflanze die nach der Bl\u00fcte etwas Zuwendung braucht. In unserem Video zeigen wir Euch, wie Ihr Lavendel nach der Bl\u00fcte schneidet und die Bl\u00fcten praktisch verwertet.\r\nWie Ihr Euren Lavendel lange gesund und kraftvoll haltet erfahrt Ihr in unserem Lavendel-Artikel: http:\/\/bit.ly\/2aXyjC3\r\n\r\nWenn euch das Video gefallen hat, schenkt uns ein \u201eGef\u00e4llt mir\u201c und abonniert unseren Kanal. Danke! Euer Online-Team von MEIN SCH\u00d6NER GARTEN\r\n_______________________________\r\nMEHR VON MEIN SCH\u00d6NER GARTEN\r\n\r\n\u2741 Facebook: https:\/\/www.facebook.com\/meinschoener...\r\n\u2741 Instagram: https:\/\/www.instagram.com\/mein_schoen...\r\n\u2741 Pinterest: https:\/\/de.pinterest.com\/schoenergarten\/\r\n\u2741 Twitter: https:\/\/twitter.com\/meingarten\r\n\u2741 Google+: http:\/\/bit.ly\/29XGEts",
        "category": "",
        "channel": "CMS",
        "videotype": "movie",
        "genre": "",
        "isPay": "0",
        "uploaded": 1463997938,
        "tags": "Testtag,Haus",
        "lat": "0",
        "lng": "0",
        "location": "",
        "country": "",
        "actors": "",
        "shows": "",
        "voices": "",
        "director": "",
        "producer": "",
        "cameraman": "",
        "scriptby": "",
        "musicby": "",
        "conductor": "",
        "studio": "0",
        "year": "2016",
        "copyright": "",
        "imagecopyright": "",
        "awards": "",
        "ages": "0",
        "hasTrailerID": "0",
        "isReferenceOf": "0",
        "linkedAlbum": "0",
        "linkedFile": 0,
        "language": "deutsch",
        "encodedTHUMBS": "1",
        "rating": 3,
        "ratingcount": 0,
        "thumb": "http:\/\/nx-i.akamaized.net\/201605\/G750452J1M6XAOWxL.jpg",
        "thumb_ssl": "https:\/\/nx-i.akamaized.net\/201605\/G750452J1M6XAOWxL.jpg",
        "thumb_alt": "http:\/\/nx-i.akamaized.net\/global\/nodata\/nodataxL.jpg",
        "thumb_alt_ssl": "https:\/\/nx-i.akamaized.net\/global\/nodata\/nodataxL.jpg",
        "thumb_action": "http:\/\/nx-i.akamaized.net\/global\/nodata\/nodataxL.jpg",
        "thumb_action_ssl": "https:\/\/nx-i.akamaized.net\/global\/nodata\/nodataxL.jpg",
        "runtime": "00:02:45",
        "hasSubtitles": 0,
        "userid": 0,
        "orientation": "landscape",
        "isTrailerOf": 0,
        "thumb_animatedgif": "http:\/\/nx-i.akamaized.net\/global\/nodata\/nodata.jpg",
        "thumb_animatedgif_ssl": "https:\/\/nx-i.akamaized.net\/global\/nodata\/nodata.jpg",
        "thumb_hasX2": "0",
        "thumb_hasX3": "0",
        "categoryname": "",
        "channel_id": "833",
        "parent_channel": 0,
        "commentcount": 0,
        "actors_ids": "19478,19479",
        "likecount": 0,
        "tags_ids": "9918",
        "studioname": "",
        "currency": "EUR",
        "discount": 0,
        "price": 0,
        "originalprice": 0,
        "genre_ids": ""
      },
      "itemStates": {
        "isSSC": 1,
        "encodedSSC": 1,
        "validfrom_ssc": 0,
        "validto_ssc": 0,
        "encodedHTML5": 1,
        "isMOBILE": 1,
        "encodedMOBILE": 1,
        "validfrom_mobile": 0,
        "validto_mobile": 0,
        "active": 1,
        "isDeleted": 0,
        "isBlocked": 0,
        "encodedTHUMBS": 1,
        "validto_image": 0,
        "autodelete": "0000-00-00",
        "georestriction": "",
        "geoexcludes": "",
        "ages": 0
      }
    }';
    return $serializedData;
  }

}
