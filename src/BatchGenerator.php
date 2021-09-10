<?php

namespace Drupal\usermerge;

use Drupal\Core\DependencyInjection\DependencySerializationTrait;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\user\UserInterface;
use Drupal\usermerge\Exception\UserMergeException;

/**
 * Class BatchGenerator.
 *
 * @see \Drupal\search_api\Task\TaskManager
 * @package Drupal\usermerge
 */
class BatchGenerator implements BatchGeneratorInterface {

  use DependencySerializationTrait;
  use StringTranslationTrait;

  /**
   * The service for merging users.
   *
   * @var \Drupal\usermerge\UserMergerInterface
   */
  protected $userMerger;

  /**
   * Provides messenger service.
   *
   * @var \Drupal\Core\Messenger\Messenger
   */
  protected $messenger;

  /**
   * Multi step storage.
   *
   * @var \Drupal\usermerge\MultiStepStorageInterface
   */
  protected $multiStepStorage;

  /**
   * The user storage.
   *
   * @var \Drupal\user\UserStorageInterface
   */
  protected $userStorage;

  /**
   * BatchGenerator constructor.
   *
   * @param \Drupal\usermerge\UserMergerInterface $user_merger
   *   The user merger service.
   * @param \Drupal\Core\Messenger\MessengerInterface $messenger
   *   The messenger service.
   * @param \Drupal\usermerge\MultiStepStorageInterface $multi_step_storage
   *   Multi step storage.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager service.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function __construct(
    UserMergerInterface $user_merger,
    MessengerInterface $messenger,
    MultiStepStorageInterface $multi_step_storage,
    EntityTypeManagerInterface $entity_type_manager) {
    $this->userMerger = $user_merger;
    $this->messenger = $messenger;
    $this->multiStepStorage = $multi_step_storage;

    $this->userStorage = $entity_type_manager->getStorage('user');
  }

  /**
   * {@inheritDoc}
   */
  public function createBatch(): void {
    $batch = [
      'title' => $this->t('Merge account...'),
      'init_message' => $this->t('Starting merge process.'),
      'progress_message' => $this->t('Completed step @current of @total.'),
      'error_message' => $this->t('Merge process has encountered an error.'),
      'operations' => [],
      'finished' => [$this, 'batchFinished'],
      'progressive' => TRUE,
    ];

    $retain_id = $this->multiStepStorage->getValueFromStore('', 'retain');
    $retire_id = $this->multiStepStorage->getValueFromStore('', 'retire');

    $definitions = $this->userMerger->getPropertyPlugins();
    foreach ($definitions as $plugin_id) {
      $settings = $this->multiStepStorage->getValuesFromStore($plugin_id);

      $batch['operations'][] = [
        [$this, 'performPropertyProcess'],
        [
          $plugin_id,
          $retire_id,
          $retain_id,
          $settings,
        ],
      ];

      $this->multiStepStorage->delete($plugin_id);
    }

    // Perform action.
    $batch['operations'][] = [
      [$this, 'performActionProcess'],
      [
        $this->multiStepStorage->getValueFromStore('', 'action'),
        $retire_id,
        $retain_id,
      ],
    ];
    $this->multiStepStorage->delete('');

    batch_set($batch);
  }

  /**
   * Runs batch steps for action plugin.
   *
   * @param string $plugin_id
   *   Property plugin id.
   * @param int $retire_id
   *   Retire user id.
   * @param int $retain_id
   *   Retain user id.
   * @param mixed &$context
   *   The Batch API context.
   *
   * @throws \Drupal\Component\Plugin\Exception\PluginException
   */
  public function performActionProcess($plugin_id, $retire_id, $retain_id, &$context) {
    if (isset($context['results']['skip'])) {
      return;
    }

    try {
      $retire = $this->loadAccount($retire_id);
      $retain = $this->loadAccount($retain_id);

      $this->userMerger->applyAction($plugin_id, $retire, $retain);
    }
    catch (UserMergeException $e) {
      $context['results']['skip'] = TRUE;
      $context['results']['error'] = $e->getMessage();
    }
  }

  /**
   * Runs batch steps for property plugin.
   *
   * @param string $plugin_id
   *   Property plugin id.
   * @param int $retire_id
   *   Retire user id.
   * @param int $retain_id
   *   Retain user id.
   * @param array $settings
   *   Review settings.
   * @param mixed &$context
   *   The Batch API context.
   *
   * @throws \Drupal\Component\Plugin\Exception\PluginException
   */
  public function performPropertyProcess($plugin_id, $retire_id, $retain_id, array $settings, &$context) {
    if (isset($context['results']['skip'])) {
      return;
    }

    try {
      $retire = $this->loadAccount($retire_id);
      $retain = $this->loadAccount($retain_id);

      $this->userMerger->applyProperty($plugin_id, $retire, $retain, $settings);
    }
    catch (UserMergeException $e) {
      $context['results']['skip'] = TRUE;
      $context['results']['error'] = $e->getMessage();
    }
  }

  /**
   * Batch callback called when the batch finishes.
   *
   * @param bool $success
   *   TRUE if batch successfully completed.
   * @param array $results
   *   Batch results.
   * @param array $operations
   *   An array of methods run in the batch.
   */
  public function batchFinished($success, array $results, array $operations) {
    if (isset($results['error'])) {
      $this->messenger->addError($results['error']);
      return;
    }

    if ($success) {
      $this->messenger->addMessage($this->t('Operations completed.'));
    }
    else {
      // An error occurred.
      // $operations contains the operations that remained unprocessed.
      $error_operation = reset($operations);
      $this->messenger->addError($this->t('An error occurred while processing @operation with arguments: @args', [
        '@operation' => $error_operation[0],
        '@args' => print_r($error_operation[0], TRUE),
      ]));
    }
  }

  /**
   * Load user.
   *
   * @param int $uid
   *   User id.
   *
   * @return \Drupal\Core\Entity\EntityInterface
   *   User entity.
   *
   * @throws \Drupal\usermerge\Exception\UserMergeException
   */
  protected function loadAccount($uid) {
    $user = $this->userStorage->load($uid);
    if (!($user instanceof UserInterface)) {
      throw new UserMergeException('Cant not find user');
    }

    return $user;
  }

}
