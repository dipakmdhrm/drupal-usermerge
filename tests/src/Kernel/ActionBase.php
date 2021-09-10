<?php

namespace Drupal\Tests\usermerge\Kernel;

use Drupal\KernelTests\Core\Entity\EntityKernelTestBase;

/**
 * Class ActionBase.
 *
 * @package Drupal\Tests\usermerge\Kernel
 */
abstract class ActionBase extends EntityKernelTestBase {

  /**
   * Test user accounts.
   *
   * @var \Drupal\user\UserInterface[]
   */
  public $users = [];

  /**
   * Current user.
   *
   * @var \Drupal\user\UserInterface
   */
  public $currentUser;

  /**
   * User Merge Action Manager.
   *
   * @var \Drupal\usermerge\Plugin\UserMergeActionPluginManager
   */
  public $userMergeActionManager;

  /**
   * {@inheritdoc}
   */
  public static $modules = [
    'system',
    'user',
    'usermerge',
  ];

  /**
   * {@inheritdoc}
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  protected function setUp() {
    parent::setUp();

    $this->users['user1'] = $this->drupalCreateUser();
    $this->users['user2'] = $this->drupalCreateUser();
    $this->users['user3'] = $this->drupalCreateUser();
    $this->users['user4'] = $this->drupalCreateUser();

    $this->currentUser = $this->drupalSetUpCurrentUser();

    /** @var \Drupal\usermerge\Plugin\UserMergeActionPluginManager $user_merge_action */
    $this->userMergeActionManager = $this->container->get('plugin.manager.usermerge.action');

    // Add the additional table schemas.
    $this->installSchema('user', ['users_data']);
  }

}
