<?php

namespace Drupal\usermerge\Plugin\UserMerge\Property;

use Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException;
use Drupal\Component\Plugin\Exception\PluginNotFoundException;
use Drupal\Core\Entity\EntityStorageException;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\flag\FlagServiceInterface;
use Drupal\user\UserInterface;
use Drupal\usermerge\Exception\UserMergeException;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class PropertyFlag.
 *
 * @UserMergeProperty(
 *   id = "property_flag",
 *   name = @Translation("Flag data"),
 *   description = @Translation("Reassigning flaging entities associated with the retired user."),
 *   review = "",
 *   provider = "flag"
 * )
 *
 * @package Drupal\usermerge\Plugin\UserMerge\Property
 */
class PropertyFlag extends UserMergePropertyBase {

  /**
   * Flag service.
   *
   * @var \Drupal\flag\FlagServiceInterface
   */
  protected $flag;

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
   * @param \Drupal\flag\FlagServiceInterface $flag
   *   Flag service.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityTypeManagerInterface $entity_type_manager, FlagServiceInterface $flag) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $entity_type_manager);

    $this->flag = $flag;
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
      $container->get('flag')
    );
  }

  /**
   * {@inheritDoc}
   */
  public function process(UserInterface $retired, UserInterface $retained, array $settings = []): void {
    try {
      $flagging_storage = $this->entityTypeManager->getStorage('flagging');
    }
    catch (PluginNotFoundException | InvalidPluginDefinitionException $e) {
      throw new UserMergeException('Storage for flagging entity has not been found.');
    }

    $flaggings_ids = $flagging_storage->getQuery()
      ->condition('uid', $retired->id())
      ->sort('flag_id')
      ->execute();

    /** @var \Drupal\flag\FlaggingInterface[] $flaggings */
    $flaggings = $flagging_storage->loadMultiple($flaggings_ids);

    try {
      foreach ($flaggings as $flagging) {
        $flag = $flagging->getFlag();
        $entity = $flagging->getFlaggable();

        if (!$flag->isFlagged($entity, $retained)) {
          $flagging->setOwner($retained);
          $flagging->save();
        }
      }
    }
    catch (EntityStorageException $e) {
      throw new UserMergeException('An error occurred during flag reassignment.');
    }
  }

}
