<?php

namespace Drupal\usermerge\Plugin;

use Drupal\Core\Plugin\DefaultPluginManager;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Traversable;

/**
 * Class UserMergePropertyPluginManager.
 *
 * @package Drupal\usermerge\Plugin
 */
class UserMergePropertyPluginManager extends DefaultPluginManager {

  /**
   * ActionPropertyPluginManager constructor.
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
      'Plugin/UserMerge/Property',
      $namespaces,
      $module_handler,
      'Drupal\usermerge\Plugin\UserMergePropertyPluginInterface',
      'Drupal\usermerge\Annotation\UserMergeProperty');

    $this->alterInfo('usermerge_property_info');
    $this->setCacheBackend($cache_backend, 'usermerge_property_plugin');
  }

}
