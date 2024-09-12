<?php

namespace Drupal\usermerge\Plugin\UserMerge\Property;

use Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException;
use Drupal\Component\Plugin\Exception\PluginNotFoundException;
use Drupal\Core\Database\Connection;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\EntityStorageException;
use Drupal\user\UserInterface;
use Drupal\usermerge\Exception\UserMergeException;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class PropertyNode.
 *
 * @UserMergeProperty(
 *   id = "property_node",
 *   name = @Translation("Node data"),
 *   description = @Translation("Reassign nodes owned by the retired user."),
 *   review = "",
 * )
 * @package Drupal\usermerge\Plugin\UserMerge\Property
 */
class PropertyNode extends UserMergePropertyBase {

  /**
   * The database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $connection;

  /**
   * Constructs a \Drupal\Component\Plugin\PluginBase object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\Core\Database\Connection $connection
   *   The database connection.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityTypeManagerInterface $entity_type_manager, Connection $connection) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $entity_type_manager);

    $this->connection = $connection;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager'),
      $container->get('database')
    );
  }

  /**
   * {@inheritDoc}
   */
  public function process(UserInterface $retired, UserInterface $retained, array $settings = []): void {
    try {
      $node_storage = $this->entityTypeManager->getStorage('node');
    }
    catch (PluginNotFoundException | InvalidPluginDefinitionException $e) {
      throw new UserMergeException('Storage for node entity has not been found.');
    }

    // Anonymize nodes (current revisions).
    $node_ids = $node_storage->getQuery()
      ->accessCheck(FALSE)
      ->condition('uid', $retired->id())
      ->execute();

    $nodes = $node_storage->loadMultiple($node_ids);

    try {
      /** @var \Drupal\node\NodeInterface $node */
      foreach ($nodes as $node) {
        $node->setOwnerId($retained->id());
        $node->save();
      }
    }
    catch (EntityStorageException $e) {
      throw new UserMergeException('An error occurred during nodes reassignment.');
    }

    // Anonymize old revisions.
    $this->connection->update('node_field_revision')
      ->fields(['uid' => $retained->id()])
      ->condition('uid', $retired->id())
      ->execute();
  }

}
