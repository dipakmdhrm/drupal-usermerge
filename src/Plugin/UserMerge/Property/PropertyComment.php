<?php

namespace Drupal\usermerge\Plugin\UserMerge\Property;

use Drupal\Core\Entity\EntityStorageException;
use Drupal\user\UserInterface;
use Drupal\usermerge\Exception\UserMergeException;
use Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException;
use Drupal\Component\Plugin\Exception\PluginNotFoundException;

/**
 * Class PropertyComment.
 *
 * @UserMergeProperty(
 *   id = "property_comment",
 *   name = @Translation("Comment data"),
 *   description = @Translation("Reassigning comments associated with the retired user."),
 *   review = "",
 *   provider = "comment"
 * )
 * @package Drupal\usermerge\Plugin\UserMerge\Property
 */
class PropertyComment extends UserMergePropertyBase {

  /**
   * {@inheritDoc}
   */
  public function process(UserInterface $retired, UserInterface $retained, array $settings = []): void {
    try {
      $comment_storage = $this->entityTypeManager->getStorage('comment');
    }
    catch (PluginNotFoundException | InvalidPluginDefinitionException $e) {
      throw new UserMergeException('Storage for comment entity has not been found.');
    }

    $comment_ids = $comment_storage->getQuery()
      ->condition('uid', $retired->id())
      ->execute();

    $comments = $comment_storage->loadMultiple($comment_ids);

    try {
      /** @var \Drupal\comment\CommentInterface $comment */
      foreach ($comments as $comment) {
        $comment->setOwnerId($retained->id());
        $comment->save();
      }
    }
    catch (EntityStorageException $e) {
      throw new UserMergeException('An error occurred during comment reassignment.');
    }
  }

}
