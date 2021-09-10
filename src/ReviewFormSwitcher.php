<?php

namespace Drupal\usermerge;

use Drupal\Component\Plugin\PluginManagerInterface;
use Drupal\usermerge\Exception\UserMergeException;

/**
 * Class ReviewFormSwitcher.
 *
 * @package Drupal\usermerge
 */
class ReviewFormSwitcher implements ReviewFormSwitcherInterface {

  /**
   * Property plugin manager.
   *
   * @var \Drupal\Component\Plugin\PluginManagerInterface
   */
  protected $propertyPluginManager;

  /**
   * List of forms used in multi step review.
   *
   * @var array
   */
  protected $formList = [];

  /**
   * MergeAccountsForm constructor.
   *
   * @param \Drupal\Component\Plugin\PluginManagerInterface $property_manager
   *   Property plugin manager.
   */
  public function __construct(PluginManagerInterface $property_manager) {
    $this->propertyPluginManager = $property_manager;
    $this->buildFormList();
  }

  /**
   * {@inheritDoc}
   */
  public function hasNext($class): bool {
    $keys = array_keys($this->formList);
    $last_key = end($keys);

    if ($this->formList[$last_key] == '\\' . $class) {
      return FALSE;
    }

    return TRUE;
  }

  /**
   * {@inheritDoc}
   */
  public function hasPrevious($class): bool {
    $list = $this->formList;
    reset($list);
    $first_key = key($list);

    if ($this->formList[$first_key] == '\\' . $class) {
      return FALSE;
    }

    return TRUE;
  }

  /**
   * {@inheritDoc}
   */
  public function getFormFromProperty($property): string {
    if (!isset($this->formList[$property])) {
      throw new UserMergeException('Provided property has not been found');
    }

    return $this->formList[$property];
  }

  /**
   * {@inheritDoc}
   */
  public function getPreviousProperty($class): string {
    $previous = '';

    foreach ($this->formList as $id => $review) {
      if ($review == '\\' . $class) {
        break;
      }

      $previous = $id;
    }

    return $previous;
  }

  /**
   * {@inheritDoc}
   */
  public function getNextProperty($class): string {
    $next = '';
    $get_next = FALSE;
    foreach ($this->formList as $id => $review) {
      if ($review == '\\' . $class) {
        $get_next = TRUE;
        continue;
      }

      if ($get_next) {
        $next = $id;
        break;
      }
    }

    return $next;
  }

  /**
   * Build form list.
   */
  private function buildFormList(): void {
    $this->formList[''] = '\Drupal\usermerge\Form\PickAccountsForm';

    $definitions = $this->propertyPluginManager->getDefinitions();
    foreach ($definitions as $definition) {
      if (empty($definition['review'])) {
        continue;
      }

      if (is_subclass_of($definition['review'], '\Drupal\usermerge\Form\MultiStepFormBase')) {
        $this->formList[$definition['id']] = $definition['review'];
      }
    }
  }

  /**
   * {@inheritDoc}
   */
  public function getPropertyFromForm($class): string {
    $property = '';

    foreach ($this->formList as $id => $review) {
      if ($review == '\\' . $class) {
        $property = $id;
      }
    }

    return $property;
  }

}
