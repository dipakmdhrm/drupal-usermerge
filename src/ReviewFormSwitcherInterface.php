<?php

namespace Drupal\usermerge;

/**
 * Interface ReviewFormSwitcherInterface.
 *
 * Helper which allows to move around the review forms.
 *
 * @package Drupal\usermerge
 */
interface ReviewFormSwitcherInterface {

  /**
   * Check if we have the previous form.
   *
   * @param string $class
   *   Multi step form class name.
   *
   * @return bool
   *   Check if we have a previous form.
   */
  public function hasPrevious($class): bool;

  /**
   * Check if we have the next form.
   *
   * @param string $class
   *   Multi step form class name.
   *
   * @return bool
   *   Check if we have a next form.
   */
  public function hasNext($class): bool;

  /**
   * Get previous property.
   *
   * @param string $class
   *   Multi step form class name.
   *
   * @return string
   *   If of the property plugin.
   */
  public function getPreviousProperty($class): string;

  /**
   * Get next property.
   *
   * @param string $class
   *   Multi step form class name.
   *
   * @return string
   *   If of the property plugin.
   */
  public function getNextProperty($class): string;

  /**
   * Get class name.
   *
   * @param string $property
   *   Id of the property plugin type.
   *
   * @return string
   *   Class name of the form
   *
   * @throws \Drupal\usermerge\Exception\UserMergeException
   */
  public function getFormFromProperty($property): string;

  /**
   * Get id of property.
   *
   * @param string $class
   *   Multi step form class name.
   *
   * @return string
   *   Property id.
   */
  public function getPropertyFromForm($class): string;

}
