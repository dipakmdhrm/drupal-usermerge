<?php

namespace Drupal\usermerge;

/**
 * Interface BatchGeneratorInterface.
 *
 * Helper which allows to generate batch process responsible for the user merge.
 *
 * @package Drupal\usermerge
 */
interface BatchGeneratorInterface {

  /**
   * Performs an operation to several values in a batch.
   */
  public function createBatch(): void;

}
