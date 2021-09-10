<?php

namespace Drupal\usermerge\Plugin\UserMerge\Property;

use Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException;
use Drupal\Component\Plugin\Exception\PluginNotFoundException;
use Drupal\Core\Database\Connection;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\user\UserInterface;
use Drupal\usermerge\Exception\UserMergeException;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class PropertyEntityReference.
 *
 * @UserMergeProperty(
 *   id = "property_entity_reference",
 *   name = @Translation("Entity reference"),
 *   description = @Translation("Reassigning user reference fields associated with the retired use"),
 *   review = "",
 * )
 * @package Drupal\usermerge\Plugin\UserMerge\Property
 */
class PropertyEntityReference extends UserMergePropertyBase {

  /**
   * The database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $connection;

  /**
   * PropertyComment constructor.
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
      $fields = $this->getUserReferenceFields();
    }
    catch (InvalidPluginDefinitionException | PluginNotFoundException $e) {
      throw new UserMergeException('An error occurred during searching for user reference fields.');
    }

    foreach ($fields as $field) {
      try {
        /** @var \Drupal\Core\Entity\Sql\DefaultTableMapping $table_mapping */
        $table_mapping = $this->entityTypeManager->getStorage($field->getTargetEntityTypeId())
          ->getTableMapping();
      }
      catch (InvalidPluginDefinitionException | PluginNotFoundException $e) {
        throw new UserMergeException('An error occurred during loading field storage.');
      }

      $tables = [];
      $tables[] = $table_mapping->getDedicatedDataTableName($field);
      $tables[] = $table_mapping->getDedicatedRevisionTableName($field);

      $field_name = $field->getName() . '_target_id';

      foreach ($tables as $table_name) {
        // It is very likely that revision table will not exist.
        $exists = $this->connection->schema()->tableExists($table_name);
        if (!$exists) {
          continue;
        }

        $this->connection->update($table_name)
          ->fields([$field_name => $retained->id()])
          ->condition($field_name, $retired->id())
          ->execute();
      }
    }
  }

  /**
   * Get user reference fields.
   *
   * @return \Drupal\Core\Field\FieldStorageDefinitionInterface[]
   *   Referenced fields.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  private function getUserReferenceFields() {
    $references = [];

    /** @var \Drupal\Core\Field\FieldStorageDefinitionInterface[] $fields */
    $fields = $this->entityTypeManager
      ->getStorage('field_storage_config')
      ->loadMultiple();
    foreach ($fields as $field) {
      if ($field->getType() != 'entity_reference') {
        continue;
      }

      if ($field->getSetting('target_type') != 'user') {
        continue;
      }

      $references[] = $field;
    }

    return $references;
  }

}
