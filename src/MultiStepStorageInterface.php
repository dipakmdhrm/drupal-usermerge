<?php

namespace Drupal\usermerge;

/**
 * Interface MultiStepStorageInterface.
 *
 * Helper which allows to store review data.
 *
 * @package Drupal\usermerge
 */
interface MultiStepStorageInterface {

  /**
   * Get default value.
   *
   * @param string $property
   *   Id of property plugin.
   * @param string $name
   *   Field name.
   *
   * @return string|null
   *   Field value.
   */
  public function getValueFromStore($property, $name);

  /**
   * Get stored values.
   *
   * @param string $property
   *   Id of property plugin.
   *
   * @return array
   *   Field value.
   */
  public function getValuesFromStore($property);

  /**
   * Save form values in store.
   *
   * @param string $property
   *   Id of property plugin.
   * @param array $values
   *   An associative array of values submitted to the form.
   *
   * @throws \Drupal\Core\TempStore\TempStoreException
   */
  public function setValues($property, array $values): void;

  /**
   * Get retired account.
   *
   * @return \Drupal\user\UserInterface|null
   *   Retired account.
   */
  public function getRetiredAccount();

  /**
   * Get retained account.
   *
   * @return \Drupal\user\UserInterface|null
   *   Retained account.
   */
  public function getRetainedAccount();

  /**
   * Remove all settings from store.
   *
   * @param string $property
   *   Id of property plugin.
   */
  public function delete($property): void;

}
