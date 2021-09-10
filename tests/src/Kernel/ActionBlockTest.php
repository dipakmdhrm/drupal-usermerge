<?php

namespace Drupal\Tests\usermerge\Kernel;

use Drupal\user\Entity\User;
use Drupal\usermerge\Exception\UserMergeException;

/**
 * Class ActionBlockTest.
 *
 * @group usermerge
 *
 * @package Drupal\Tests\usermerge\Kernel
 * @coversDefaultClass \Drupal\usermerge\Plugin\UserMerge\Action\ActionBlock
 */
class ActionBlockTest extends ActionBase {

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

    $this->action = $this->userMergeActionManager->createInstance('action_block');
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
   * Test scenario if retired account is already blocked.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   * @throws \Drupal\usermerge\Exception\UserMergeException
   */
  public function testAlreadyBlocked() {
    $user2 = $this->users['user2'];
    $user2->block();
    $user2->save();

    $this->setExpectedException(UserMergeException::class, 'The retire user is already blocked.');
    $this->action->process($this->users['user2'], $this->users['user2']);
  }

  /**
   * Check if user was blocked.
   *
   * @throws \Drupal\usermerge\Exception\UserMergeException
   */
  public function testProcess() {
    $retired_id = $this->users['user3']->id();
    $retained_id = $this->users['user4']->id();

    $this->action->process($this->users['user3'], $this->users['user4']);

    /** @var \Drupal\user\UserInterface $retired */
    $retired = User::load($retired_id);
    /** @var \Drupal\user\UserInterface $retained */
    $retained = User::load($retained_id);

    $this->assertInstanceOf(User::class, $retired);
    $this->assertInstanceOf(User::class, $retained);

    $this->assertTrue($retired->isBlocked());
    $this->assertFalse($retained->isBlocked());
  }

}
