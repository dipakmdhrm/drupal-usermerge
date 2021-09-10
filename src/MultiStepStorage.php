<?php

namespace Drupal\usermerge;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\TempStore\PrivateTempStoreFactory;
use Drupal\user\UserInterface;

/**
 * Class MultiStepStorage.
 *
 * @package Drupal\usermerge
 */
class MultiStepStorage implements MultiStepStorageInterface {

  /**
   * Storage for form data.
   *
   * @var \Drupal\Core\TempStore\PrivateTempStore
   */
  protected $store;

  /**
   * The user storage.
   *
   * @var \Drupal\user\UserStorageInterface
   */
  protected $userStorage;

  /**
   * MultiStepFormBase constructor.
   *
   * @param \Drupal\Core\TempStore\PrivateTempStoreFactory $temp_store_factory
   *   The temp store factory.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager service.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function __construct(PrivateTempStoreFactory $temp_store_factory, EntityTypeManagerInterface $entity_type_manager) {
    $this->store = $temp_store_factory->get('usermerge');
    $this->userStorage = $entity_type_manager->getStorage('user');
  }

  /**
   * {@inheritDoc}
   */
  public function setValues($property, array $values): void {
    // Remove not needed values.
    $skip_values = ['form_build_id', 'form_token', 'form_id'];
    foreach ($skip_values as $skip_value) {
      if (isset($values[$skip_value])) {
        unset($values[$skip_value]);
      }
    }

    // Skip values which are not a string.
    foreach ($values as $key => $value) {
      if (is_object($value)) {
        unset($values[$key]);
      }
    }

    $this->store->set($property, $values);
  }

  /**
   * {@inheritDoc}
   */
  public function getValueFromStore($property, $name) {
    $values = $this->store->get($property);

    if (isset($values[$name])) {
      return $values[$name];
    }

    return NULL;
  }

  /**
   * {@inheritDoc}
   */
  public function getValuesFromStore($property) {
    $values = $this->store->get($property);

    if (is_null($values)) {
      $values = [];
    }

    return $values;
  }

  /**
   * {@inheritDoc}
   */
  public function getRetiredAccount() {
    $values = $this->store->get('');

    if (!isset($values['retire'])) {
      return NULL;
    }

    $user = $this->userStorage->load($values['retire']);
    if ($user instanceof UserInterface) {
      return $user;
    }

    return NULL;
  }

  /**
   * {@inheritDoc}
   */
  public function getRetainedAccount() {
    $values = $this->store->get('');

    if (!isset($values['retain'])) {
      return NULL;
    }

    $user = $this->userStorage->load($values['retain']);
    if ($user instanceof UserInterface) {
      return $user;
    }

    return NULL;
  }

  /**
   * {@inheritDoc}
   *
   * @throws \Drupal\Core\TempStore\TempStoreException
   */
  public function delete($property): void {
    $this->store->delete($property);
  }

}
