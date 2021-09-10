<?php

namespace Drupal\usermerge\Plugin;

use Drupal\Core\Plugin\DefaultPluginManager;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Traversable;

/**
 * Class UserMergeActionPluginManager.
 *
 * @package Drupal\usermerge\Plugin.
 */
class UserMergeActionPluginManager extends DefaultPluginManager {

  /**
   * UserMergeActionPluginManager constructor.
   *
   * @param \Traversable $namespaces
   *   An object that implements \Traversable which contains the root paths
   *   keyed by the corresponding namespace to look for plugin implementations.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache_backend
   *   Cache backend instance to use.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler to invoke the alter hook with.
   */
  public function __construct(Traversable $namespaces, CacheBackendInterface $cache_backend, ModuleHandlerInterface $module_handler) {
    parent::__construct(
      'Plugin/UserMerge/Action',
      $namespaces,
      $module_handler,
      'Drupal\usermerge\Plugin\UserMergeActionPluginInterface',
      'Drupal\usermerge\Annotation\UserMergeAction');

    $this->alterInfo('usermerge_action_info');
    $this->setCacheBackend($cache_backend, 'usermerge_action_plugin');
  }

}
