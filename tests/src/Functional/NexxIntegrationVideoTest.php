<?php

namespace Drupal\Tests\nexx_integration\Functional;

use Drupal\taxonomy\Entity\Vocabulary;
use Drupal\taxonomy\Entity\Term;
use Drupal\Core\Url;
use Drupal\Tests\BrowserTestBase;
use Drupal\field_ui\Tests\FieldUiTestTrait;

/**
 * Test infinite_base admin interface.
 *
 * @group nexx_integration
 */
class NexxIntegrationVideoTest extends BrowserTestBase {

  use FieldUiTestTrait;

  const TEST_NEXX_VIDEO_ID = 1;

  const TEST_VOCABULARIES = ['testChannel', 'testActor', 'testTags'];

  /**
   * The database service.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $database;

  /**
   * The nexx configuration.
   *
   * @var \Drupal\Core\Config\Config
   */
  protected $config;

  /**
   * The admin user.
   *
   * @var Drupal\user\Entity\UserInterface
   */
  protected $adminUser;

  /**
   * The video edit user.
   *
   * @var Drupal\user\Entity\UserInterface
   */
  protected $videoUser;

  /**
   * A list of vocabularies keyed by name.
   *
   * @var array
   */
  protected $vocabularies;

  /**
   * List of lists of terms keyed by vocabulary name.
   *
   * The structure of this is:
   * [
   *  [vocabulary1] => [term1, term2]
   *  [vocabulary2] => [term1, term2]
   * ]
   *
   * @var array
   */
  protected $terms;

  /**
   * The field entity storage definition.
   *
   * @var array
   */
  protected $fieldStorageDefinition;

  /**
   * The field entity definition.
   *
   * @var array
   */
  protected $fieldDefinition;

  public static $modules = [
    'taxonomy',
    'nexx_integration',
    'field_ui',
    'field',
  ];

  /**
   * Setup the tests.
   */
  protected function setUp() {
    parent::setUp();
    $this->database = $this->container->get('database');
    // Prepare some users.
    $this->adminUser = $this->drupalCreateUser([], NULL, TRUE);
    $this->videoUser = $this->drupalCreateUser(['use omnia notification gateway']);
    $this->setUpTaxonomy();
    $this->attachFields();
    $this->setTestConfig();
    $this->config = $this->config('nexx_integration.settings');
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
   * Test the endpoint.
   */
  public function testVideoEndpoint() {
    $data = $this->getTestVideoData();

    // Test connectivity.
    $videoData = $this->postVideoData($data);
    $this->assertEquals($data->itemID, $videoData->refnr);
  }

  /**
   * Test the created video entity.
   */
  public function testBasicVideoCreation() {
    $data = $this->getTestVideoData();
    $videoData = $this->postVideoData($data);

    $videoEntity = $this->loadVideoEntity($videoData->value);
    $videoField = $videoEntity->get('field_video');

    $this->assertEquals($data->itemData->itemID, $videoField->item_id);
    $this->assertEquals($data->itemData->title, $videoField->title);
    $this->assertEquals($data->itemData->hash, $videoField->hash);
    $this->assertEquals($data->itemData->teaser, $videoField->teaser);
    $this->assertEquals($data->itemData->uploaded, $videoField->uploaded);
    $this->assertEquals($data->itemData->copyright, $videoField->copyright);
    $this->assertEquals($data->itemData->encodedTHUMBS, $videoField->encodedTHUMBS);
    $this->assertEquals($data->itemData->runtime, $videoField->runtime);
    $this->assertEquals($data->itemStates->isSSC, $videoField->isSSC);
    $this->assertEquals($data->itemStates->encodedSSC, $videoField->encodedSSC);
    $this->assertEquals($data->itemStates->validfrom_ssc, $videoField->validfrom_ssc);
    $this->assertEquals($data->itemStates->validto_ssc, $videoField->validto_ssc);
    $this->assertEquals($data->itemStates->encodedHTML5, $videoField->encodedHTML5);
    $this->assertEquals($data->itemStates->isMOBILE, $videoField->isMOBILE);
    $this->assertEquals($data->itemStates->encodedMOBILE, $videoField->encodedMOBILE);
    $this->assertEquals($data->itemStates->validfrom_mobile, $videoField->validfrom_mobile);
    $this->assertEquals($data->itemStates->validto_mobile, $videoField->validto_mobile);
    $this->assertEquals($data->itemStates->active, $videoField->active);
    $this->assertEquals($data->itemStates->isDeleted, $videoField->isDeleted);
    $this->assertEquals($data->itemStates->isBlocked, $videoField->isBlocked);
    $this->assertEquals($data->itemStates->encodedTHUMBS, 1);
  }

  /**
   * Test the created video entity.
   */
  public function testMappedFields() {
    /* Disabled for now */
    return;
    /*
    $data = $this->getTestVideoData();
    $videoData = $this->postVideoData($data);

    $videoEntity = $this->loadVideoEntity($videoData->value);

    // $videoField = $videoEntity->get('field_video');
     */
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
    $tags = [];
    foreach ($this->terms['testTags'] as $tag) {
      $tags[] = $this->mapOmniaTermId($tag->id());
    }
    $actors = [];
    foreach ($this->terms['testActor'] as $actor) {
      $actors[] = $this->mapOmniaTermId($actor->id());
    }
    $channel = $this->mapOmniaTermId($this->terms['testChannel'][0]->id());

    $itemData = new \stdClass();
    $itemData->itemID = self::TEST_NEXX_VIDEO_ID;
    $itemData->hash = "GL7ADZXZJ" . self::TEST_NEXX_VIDEO_ID . "P";
    $itemData->connector = "612";
    $itemData->title = "Test Video";
    $itemData->teaser = "The teaser text.";
    $itemData->description = "The description text";
    $itemData->uploaded = 1463997938;
    $itemData->copyright = "Copyright notice";
    $itemData->encodedTHUMBS = "1";
    $itemData->thumb = "http://nx-i.akamaized.net/201605/G750452J1M6XAOWxL.jpg";
    $itemData->runtime = "00:02:45";
    $itemData->channel_id = $channel;
    $itemData->actors_ids = implode(',', $actors);
    $itemData->tags_ids = implode(',', $tags);

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

    $baseData = new \stdClass();
    $baseData->itemID = self::TEST_NEXX_VIDEO_ID;
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

  /**
   * Map drupal term Id to corresponding omnia term id.
   *
   * @param int $tid
   *    The term id of the term.
   *
   * @return int
   *    The omnia id of the term.
   */
  protected function mapOmniaTermId($tid) {
    $result = $this->database->select('nexx_taxonomy_term_data', 'n')
      ->fields('n', array('nexx_item_id'))
      ->condition('n.tid', $tid)
      ->execute();

    $drupal_id = $result->fetchField();

    return $drupal_id;
  }

  /**
   * Attach fields to media bundle using the FieldUiTestTrait.
   */
  protected function attachFields() {
    /* Disabled for now */
    return;
    /*
    $this->drupalLogin($this->adminUser);
    $bundle_path = 'admin/structure/media/manage/nexx_video';

    $this->submitFieldUi($bundle_path, 'test_description', NULL, 'text_long');

    foreach ($this->vocabularies as $vocabulary) {
    $storage_edit = [];
    $field_edit = [];
    $storage_edit['settings[target_type]'] = 'taxonomy_term';
    $storage_edit['cardinality_number'] = '2';
    $field_edit['settings[handler_settings][target_bundles][' .
    $vocabulary->id() . ']'] = TRUE;
    $this->submitFieldUi($bundle_path, 'test_' . $vocabulary->label(), NULL,
    'field_ui:entity_reference:taxonomy_term');
    }
     */
  }

  /**
   * Creates a new field through the Field UI.
   *
   * @param string $bundle_path
   *   Admin path of the bundle that the new field is to be attached to.
   * @param string $field_name
   *   The field name of the new field storage.
   * @param string $label
   *   (optional) The label of the new field. Defaults to a random string.
   * @param string $field_type
   *   (optional) The field type of the new field storage. Defaults to
   *   'test_field'.
   * @param array $storage_edit
   *   (optional) $edit parameter for drupalPostForm() on the second step
   *   ('Storage settings' form).
   * @param array $field_edit
   *   (optional) $edit parameter for drupalPostForm() on the third step ('Field
   *   settings' form).
   */
  protected function submitFieldUi($bundle_path, $field_name, $label = NULL, $field_type = 'test_field', array $storage_edit = array(), array $field_edit = array()) {
    $label = $label ?: $this->randomString();
    $initial_edit = array(
      'new_storage_type' => $field_type,
      'label' => $label,
      'field_name' => $field_name,
    );

    // Allow the caller to set a NULL path in case they navigated to the right
    // page before calling this method.
    if ($bundle_path !== NULL) {
      $bundle_path = "$bundle_path/fields/add-field";
    }

    // First step: 'Add field' page.
    $this->drupalPostForm($bundle_path, $initial_edit, t('Save and continue'));

    // Second step: 'Storage settings' form.
    $this->drupalPostForm(NULL, $storage_edit, t('Save field settings'));

    // Third step: 'Field settings' form.
    $this->drupalPostForm(NULL, $field_edit, t('Save settings'));
  }

  /**
   * Setup taxonomy vocabularies and terms.
   */
  protected function setUpTaxonomy() {
    // Create vocabularies for channel, actor and tags.
    $this->vocabularies = [];

    foreach (self::TEST_VOCABULARIES as $vocabularyName) {
      $this->vocabularies[$vocabularyName] = Vocabulary::create(['vid' => $vocabularyName]);
      $this->vocabularies[$vocabularyName]->save();
      $this->terms[$vocabularyName] = [];

      // Populate the vocabulary with two terms.
      for ($i = 0; $i < 2; $i++) {
        $term = Term::create([
          'name' => $vocabularyName . ' Term ' . $i,
          'vid' => $vocabularyName,
        ]);
        $term->save();
        $this->terms[$vocabularyName][$i] = $term;

        // Mapping an omnia ID.
        update_nexx_term_id_mapping($term->id(), ($term->id() + 100));
      }
    }
  }

}
