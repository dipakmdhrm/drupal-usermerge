<?php

namespace Drupal\Tests\usermerge\Kernel;

use Drupal\user\Entity\User;
use Drupal\usermerge\Exception\UserMergeException;

/**
 * Class ActionDeleteTest.
 *
 * @group usermerge
 *
 * @package Drupal\Tests\usermerge\Kernel
 * @coversDefaultClass \Drupal\usermerge\Plugin\UserMerge\Action\ActionDelete
 */
class ActionDeleteTest extends ActionBase {

  /**
   * User merge action.
   *
   * @var \Drupal\usermerge\Plugin\UserMergeActionPluginInterface
   */
  public $action;

  /**
   * {@inheritdoc}
   *
   * @throws \Drupal\Component\Plugin\Exception\PluginException
   */
  protected function setUp() {
    parent::setUp();

    $this->action = $this->userMergeActionManager->createInstance('action_delete');
  }

  /**
   * Test deletion of user with id 1.
   *
   * @throws \Drupal\usermerge\Exception\UserMergeException
   */
  public function testDeleteAdministrationAccount() {
    $this->setExpectedException(UserMergeException::class, 'You can not retire user 1.');
    $this->action->process($this->users['user1'], $this->users['user2']);
  }

  /**
   * Test deletion of logged in account.
   *
   * @throws \Drupal\usermerge\Exception\UserMergeException
   */
  public function testDeleteSelf() {
    $this->setExpectedException(UserMergeException::class, 'You can not retire self.');
    $this->action->process($this->currentUser, $this->users['user2']);
  }

  /**
   * Check if user was deleted.
   *
   * @throws \Drupal\usermerge\Exception\UserMergeException
   */
  public function testProcess() {
    $retired_id = $this->users['user3']->id();
    $retained_id = $this->users['user4']->id();

    $this->action->process($this->users['user3'], $this->users['user4']);

    $this->assertNull(User::load($retired_id));
    $this->assertInstanceOf(User::class, User::load($retained_id));
  }

}
