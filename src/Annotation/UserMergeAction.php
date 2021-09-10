<?php

namespace Drupal\usermerge\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Defines a action merge plugin annotation object.
 *
 * @Annotation
 */
class UserMergeAction extends Plugin {

  /**
   * The plugin ID.
   *
   * @var string
   */
  public $id;

  /**
   * The name of the form plugin.
   *
   * @var \Drupal\Core\Annotation\Translation
   *
   * @ingroup plugin_translatable
   */
  public $name;

}
