<?php

namespace Drupal\usermerge\Plugin\UserMerge\Property;

use Drupal\Core\Entity\EntityStorageException;
use Drupal\user\UserInterface;
use Drupal\usermerge\Exception\UserMergeException;
use Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException;
use Drupal\Component\Plugin\Exception\PluginNotFoundException;

/**
 * Class PropertyMessage.
 *
 * @UserMergeProperty(
 *   id = "property_message",
 *   name = @Translation("Message data"),
 *   description = @Translation("Reassign message entities associated with the retired user."),
 *   review = "",
 *   provider = "message"
 * )
 * @package Drupal\usermerge\Plugin\UserMerge\Property
 */
class PropertyMessage extends UserMergePropertyBase {

  /**
   * {@inheritDoc}
   */
  public function process(UserInterface $retired, UserInterface $retained, array $settings = []): void {
    try {
      $message_storage = $this->entityTypeManager->getStorage('message');
    }
    catch (PluginNotFoundException | InvalidPluginDefinitionException $e) {
      throw new UserMergeException('Storage for message entity has not been found.');
    }

    $message_ids = $message_storage->getQuery()
      ->accessCheck(FALSE)
      ->condition('uid', $retired->id())
      ->execute();

    $messages = $message_storage->loadMultiple($message_ids);

    try {
      /** @var \Drupal\message\MessageInterface $message */
      foreach ($messages as $message) {
        $message->setOwnerId($retained->id());
        $message->save();
      }
    }
    catch (EntityStorageException $e) {
      throw new UserMergeException('An error occurred during message reassignment.');
    }
  }

}
