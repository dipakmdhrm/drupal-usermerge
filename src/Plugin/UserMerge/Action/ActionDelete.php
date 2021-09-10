<?php

namespace Drupal\usermerge\Plugin\UserMerge\Action;

use Drupal\user\UserInterface;
use Drupal\usermerge\Exception\UserMergeException;
use Drupal\Core\Entity\EntityStorageException;

/**
 * Class ActionDelete.
 *
 * @UserMergeAction(
 *   id = "action_delete",
 *   name = @Translation("Delete retired account"),
 * )
 *
 * @package Drupal\usermerge\Plugin\UserMerge\Action
 */
class ActionDelete extends UserMergeActionBase {

  /**
   * {@inheritDoc}
   */
  public function process(UserInterface $retired, UserInterface $retained): void {
    if ($retired->id() == 1) {
      throw new UserMergeException('You can not retire user 1.');
    }

    if ($retired->id() == $this->currentUser->id()) {
      throw new UserMergeException('You can not retire self.');
    }

    try {
      $retired->delete();
    }
    catch (EntityStorageException $e) {
      throw new UserMergeException('An error occurred during deletion.');
    }
  }

}
