<?php

namespace Drupal\usermerge;

use Drupal\user\UserInterface;

/**
 * A service for merging two user accounts together.
 */
interface UserMergerInterface {

  /**
   * Merges two user accounts.
   *
   * @param \Drupal\user\UserInterface $retire_user
   *   The user being retired after the merge is complete.
   * @param \Drupal\user\UserInterface $retain_user
   *   The user being retained after the merge is complete.
   * @param string $action_id
   *   The id of the action plugin to be applied to the account being retired.
   *
   * @return \Drupal\user\UserInterface
   *   The final merged account.
   */
  public function merge(UserInterface $retire_user, UserInterface $retain_user, $action_id = 'action_block');

  /**
   * Applies the action to take on the user account being retired.
   *
   * @param string $plugin_id
   *   The action plugin id to use.
   * @param \Drupal\user\UserInterface $retire_user
   *   The user being retired after the merge is complete.
   * @param \Drupal\user\UserInterface $retain_user
   *   The user being retained after the merge is complete.
   */
  public function applyAction($plugin_id, UserInterface $retire_user, UserInterface $retain_user);

  /**
   * Applies a property plugin to a set of accounts being merged.
   *
   * @param string $plugin_id
   *   The property plugin id to use.
   * @param \Drupal\user\UserInterface $retire_user
   *   The user being retired after the merge is complete.
   * @param \Drupal\user\UserInterface $retain_user
   *   The user being retained after the merge is complete.
   * @param array $settings
   *   The property plugin settings.
   */
  public function applyProperty($plugin_id, UserInterface $retire_user, UserInterface $retain_user, array $settings = []);

  /**
   * Gets a list of property plugin ids.
   *
   * @return string[]
   *   An array of property plugin ids.
   */
  public function getPropertyPlugins();

}
