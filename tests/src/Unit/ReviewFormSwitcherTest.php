<?php

namespace Drupal\Tests\usermerge\Unit;

use Drupal\Component\Plugin\PluginManagerBase;
use Drupal\Tests\UnitTestCase;
use Drupal\usermerge\Exception\UserMergeException;
use Drupal\usermerge\ReviewFormSwitcher;

/**
 * Class ReviewFormSwitcherTest.
 *
 * @package Drupal\Tests\usermerge\Unit
 */
class ReviewFormSwitcherTest extends UnitTestCase {

  /**
   * Sample Definition.
   *
   * @var array
   */
  private $sampleDefinition;

  /**
   * Review Form Switcher object.
   *
   * @var \Drupal\usermerge\ReviewFormSwitcher
   */
  private $reviewFormSwitcher;

  /**
   * {@inheritDoc}
   */
  public function setUp() {
    parent::setUp();

    $this->sampleDefinition = [
      [
        'review' => '\Drupal\usermerge\Form\ReviewUserForm',
        'id' => 'random_id',
      ],
    ];
    $property_manager = $this->getMockBuilder(PluginManagerBase::class)->disableOriginalConstructor()
      ->getMock();
    $property_manager->expects($this->any())
      ->method('getDefinitions')
      ->willReturn($this->sampleDefinition);
    $this->reviewFormSwitcher = new ReviewFormSwitcher($property_manager);
  }

  /**
   * {@inheritDoc}
   */
  public function testHasNext() {
    $this->assertFalse($this->reviewFormSwitcher->hasNext('Drupal\usermerge\Form\ReviewUserForm'));
    $this->assertTrue($this->reviewFormSwitcher->hasNext('Drupal\usermerge\Form\PickAccountsForm'));
  }

  /**
   * {@inheritDoc}
   */
  public function testHasPrevious() {
    $this->assertFalse($this->reviewFormSwitcher->hasPrevious('Drupal\usermerge\Form\PickAccountsForm'));
    $this->assertTrue($this->reviewFormSwitcher->hasPrevious('Drupal\usermerge\Form\ReviewUserForm'));
  }

  /**
   * {@inheritDoc}
   */
  public function testGetFormFromPropertyExeption() {
    $this->expectException(UserMergeException::class);
    $this->reviewFormSwitcher->getFormFromProperty('nonExistingArrayKey');
  }

  /**
   * {@inheritDoc}
   */
  public function testGetFormFromProperty() {
    $return = $this->reviewFormSwitcher->getFormFromProperty('random_id');
    $this->assertEquals($this->sampleDefinition[0]['review'], $return);
  }

  /**
   * {@inheritDoc}
   */
  public function testGetPreviousProperty() {
    $return = $this->reviewFormSwitcher->getPreviousProperty('Drupal\usermerge\Form\ReviewUserForm');
    $this->assertEquals('', $return);
    $return = $this->reviewFormSwitcher->getPreviousProperty('NotExisting');
    $this->assertEquals('random_id', $return);
  }

  /**
   * {@inheritDoc}
   */
  public function testGetNextProperty() {
    $return = $this->reviewFormSwitcher->getNextProperty('Drupal\usermerge\Form\PickAccountsForm');
    $this->assertEquals('random_id', $return);
    $return = $this->reviewFormSwitcher->getNextProperty('Drupal\usermerge\Form\ReviewUserForm');
    $this->assertEquals('', $return);
  }

  /**
   * {@inheritDoc}
   */
  public function testGetPropertyFromForm() {
    $return = $this->reviewFormSwitcher->getPropertyFromForm('Drupal\usermerge\Form\PickAccountsForm');
    $this->assertEquals('', $return);
    $return = $this->reviewFormSwitcher->getPropertyFromForm('Drupal\usermerge\Form\ReviewUserForm');
    $this->assertEquals('random_id', $return);
  }

}
