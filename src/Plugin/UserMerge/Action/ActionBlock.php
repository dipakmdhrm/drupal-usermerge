<?php

namespace Drupal\usermerge\Plugin\UserMerge\Action;

use Drupal\user\UserInterface;
use Drupal\usermerge\Exception\UserMergeException;
use Drupal\Core\Entity\EntityStorageException;

/**
 * Class ActionBlock.
 *
 * @UserMergeAction(
 *   id = "action_block",
 *   name = @Translation("Block retired account"),
 * )
 *
 * @package Drupal\usermerge\Plugin\UserMerge\Action
 */
class ActionBlock extends UserMergeActionBase {

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

    if (!$retired->isActive()) {
      throw new UserMergeException('The retire user is already blocked.');
    }

    try {
      $retired->block();
      $retired->save();
    }
    catch (EntityStorageException $e) {
      throw new UserMergeException('Am error occurred during status change.');
    }
  }

}
