<?php

namespace Drupal\usermerge\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Defines a property merge plugin annotation object.
 *
 * @Annotation
 */
class UserMergeProperty extends Plugin {

  /**
   * The plugin ID.
   *
   * @var string
   */
  public $id;

  /**
   * The name of the property plugin.
   *
   * @var \Drupal\Core\Annotation\Translation
   *
   * @ingroup plugin_translatable
   */
  public $name;

  /**
   * The plugin description.
   *
   * @var \Drupal\Core\Annotation\Translation
   *
   * @ingroup plugin_translatable
   */
  public $description;

  /**
   * Full class name of the review form, with leading backslash.
   *
   * Class must extend "\Drupal\usermerge\Form\MultiStepFormBase".
   *
   * @var string
   */
  public $review = '';

}
