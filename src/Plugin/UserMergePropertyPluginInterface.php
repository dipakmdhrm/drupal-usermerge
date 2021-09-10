<?php

namespace Drupal\usermerge\Plugin;

use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Component\Plugin\PluginInspectionInterface;
use Drupal\user\UserInterface;

/**
 * Interface UserMergePropertyPluginInterface.
 *
 * Plugin type which allow to inject process triggered on specific entities.
 *
 * @package Drupal\usermerge\Plugin
 */
interface UserMergePropertyPluginInterface extends PluginInspectionInterface, ContainerFactoryPluginInterface {

  /**
   * Return the name of the property plugin.
   *
   * @return string
   *   Action name.
   */
  public function getName();

  /**
   * Get the review form if it is provided by the plugin.
   *
   * @return string
   *   Review name.
   */
  public function getReviewForm();

  /**
   * Process merge on selected property.
   *
   * @param \Drupal\user\UserInterface $retired
   *   Retired account.
   * @param \Drupal\user\UserInterface $retained
   *   Retained account.
   * @param array $settings
   *   Review settings.
   *
   * @throws \Drupal\usermerge\Exception\UserMergeException
   */
  public function process(UserInterface $retired, UserInterface $retained, array $settings = []): void;

}
