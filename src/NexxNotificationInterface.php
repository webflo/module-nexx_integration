<?php

namespace Drupal\nexx_integration;

/**
 * Interface NexxNotificationInterface.
 *
 * @package Drupal\nexx_integration
 */
interface NexxNotificationInterface {

  /**
   * Insert taxonomy terms into omnia CMS.
   *
   * @param $streamtype
   *   Type of data to be inserted. Allowed values are:
   *        - actor: Actor
   *        - channel: Video channel
   *        - tag: arbitrary tag
   * @param $reference_number
   *   Drupal id of the given taxonomy term.
   * @param $value
   *   Name of taxonomy term.
   */
  function insert($streamtype, $reference_number, $value);

  /**
   * Update taxonomy term or video reference numbers in omnia CMS.
   *
   * For taxonomy terms, this can update the name, for videos this updates the
   * media id.
   *
   * @param $streamtype
   *   Type of data to be updated. Allowed values are:
   *        - actor: Actor
   *        - channel: Video channel
   *        - tag: Arbitrary tag
   *        - video: Video
   * @param $reference_number
   *   Drupal id of the given taxonomy term, in case of streamtype "video"
   *   this is the reference number of the video inside of Omnia,
   *   not the drupal media ID!
   * @param $value
   *   Name of taxonomy term, or drupal media id when updating a video.
   */
  function update($streamtype, $reference_number, $value);

  /**
   * Delete taxonomy terms from omnia CMS.
   *
   * @param $streamtype
   *   Type of data to be inserted. Allowed values are:
   *        - actor: Actor
   *        - channel: Video channel
   *        - tag: arbitrary tag
   * @param $reference_number
   *   Drupal id of the given taxonomy term
   */
  function delete($streamtype, $reference_number, $values);

}
