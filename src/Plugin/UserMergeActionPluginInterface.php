<?php

namespace Drupal\usermerge\Plugin;

use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Component\Plugin\PluginInspectionInterface;
use Drupal\user\UserInterface;

/**
 * Interface UserMergeActionPluginInterface.
 *
 * Plugin type which allow to inject a custom action.
 *
 * @package Drupal\usermerge\Plugin
 */
interface UserMergeActionPluginInterface extends PluginInspectionInterface, ContainerFactoryPluginInterface {

  /**
   * Return the name of the action merge plugin.
   *
   * @return string
   *   Action name.
   */
  public function getName();

  /**
   * Process merge on selected property.
   *
   * @param \Drupal\user\UserInterface $retired
   *   Retired account.
   * @param \Drupal\user\UserInterface $retained
   *   Retained account.
   *
   * @throws \Drupal\usermerge\Exception\UserMergeException
   */
  public function process(UserInterface $retired, UserInterface $retained): void;

}
