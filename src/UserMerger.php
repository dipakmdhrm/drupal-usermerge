<?php

namespace Drupal\usermerge;

use Drupal\Component\Plugin\PluginManagerInterface;
use Drupal\user\UserInterface;

/**
 * A service for merging two Drupal user accounts.
 */
class UserMerger implements UserMergerInterface {

  /**
   * Property plugin manager.
   *
   * @var \Drupal\Component\Plugin\PluginManagerInterface
   */
  protected $propertyPluginManager;

  /**
   * Action plugin manager.
   *
   * @var \Drupal\Component\Plugin\PluginManagerInterface
   */
  protected $actionPluginManager;

  /**
   * Creates a user merger object.
   *
   * @param \Drupal\Component\Plugin\PluginManagerInterface $property_manager
   *   Property plugin manager.
   * @param \Drupal\Component\Plugin\PluginManagerInterface $action_manager
   *   Action plugin manager.
   */
  public function __construct(PluginManagerInterface $property_manager, PluginManagerInterface $action_manager) {
    $this->propertyPluginManager = $property_manager;
    $this->actionPluginManager = $action_manager;
  }

  /**
   * {@inheritdoc}
   */
  public function merge(UserInterface $retire_user, UserInterface $retain_user, $action_id = 'action_block') {
    $this->applyAction($action_id, $retire_user, $retain_user);

    foreach ($this->getPropertyPlugins() as $plugin_id) {
      $this->applyProperty($plugin_id, $retire_user, $retain_user);
    }

    return $retain_user;
  }

  /**
   * {@inheritdoc}
   */
  public function applyAction($plugin_id, UserInterface $retire_user, UserInterface $retain_user) {
    $this->actionPluginManager->createInstance($plugin_id)
      ->process($retire_user, $retain_user);
  }

  /**
   * {@inheritdoc}
   */
  public function applyProperty($plugin_id, UserInterface $retire_user, UserInterface $retain_user, array $settings = []) {
    $this->propertyPluginManager->createInstance($plugin_id)
      ->process($retire_user, $retain_user, $settings);
  }

  /**
   * {@inheritdoc}
   */
  public function getPropertyPlugins() {
    $plugin_ids = [];

    foreach ($this->propertyPluginManager->getDefinitions() as $plugin_id => $definition) {
      $plugin_ids[] = $plugin_id;
    }

    return $plugin_ids;
  }

}
