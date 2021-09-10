<?php

namespace Drupal\usermerge\Plugin\UserMerge\Property;

use Drupal\user\UserInterface;

/**
 * Class PropertyUser.
 *
 * @UserMergeProperty(
 *   id = "property_user",
 *   name = @Translation("Account data"),
 *   description = @Translation("Choose which user information (default properties and custom fields, if available) should be kept, discarded, or merged."),
 *   review = "\Drupal\usermerge\Form\ReviewUserForm",
 * )
 * @package Drupal\usermerge\Plugin\UserMerge\Property
 */
class PropertyUser extends UserMergePropertyBase {

  /**
   * {@inheritDoc}
   */
  public function process(UserInterface $retired, UserInterface $retained, array $settings = []): void {
    // @TODO: allow to pick fields which will be merged.
  }

}
