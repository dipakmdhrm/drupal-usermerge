<?php

namespace Drupal\usermerge\Form;

use Drupal\Component\Plugin\PluginManagerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\usermerge\BatchGeneratorInterface;
use Drupal\usermerge\MultiStepStorageInterface;
use Drupal\usermerge\ReviewFormSwitcherInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class PickAccountsForm.
 *
 * @package Drupal\usermerge\Form
 */
class PickAccountsForm extends MultiStepFormBase {

  /**
   * Action plugin manager.
   *
   * @var \Drupal\Component\Plugin\PluginManagerInterface
   */
  protected $actionPluginManager;

  /**
   * Property plugin manager.
   *
   * @var \Drupal\Component\Plugin\PluginManagerInterface
   */
  protected $propertyPluginManager;

  /**
   * MergeAccountsForm constructor.
   *
   * @param \Drupal\usermerge\MultiStepStorageInterface $multi_step_storage
   *   Multi step storage.
   * @param \Drupal\usermerge\ReviewFormSwitcherInterface $review_switcher
   *   Review form switcher.
   * @param \Drupal\usermerge\BatchGeneratorInterface $batch_generator
   *   Batch generator.
   * @param \Drupal\Component\Plugin\PluginManagerInterface $action_manager
   *   Action plugin manager.
   * @param \Drupal\Component\Plugin\PluginManagerInterface $property_manager
   *   Property plugin manager.
   */
  public function __construct(
    MultiStepStorageInterface $multi_step_storage,
    ReviewFormSwitcherInterface $review_switcher,
    BatchGeneratorInterface $batch_generator,
    PluginManagerInterface $action_manager,
    PluginManagerInterface $property_manager
  ) {
    parent::__construct($multi_step_storage, $review_switcher, $batch_generator);

    $this->actionPluginManager = $action_manager;
    $this->propertyPluginManager = $property_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('usermerge.multi_step_storage'),
      $container->get('usermerge.review_form_switcher'),
      $container->get('usermerge.batch_generator'),
      $container->get('plugin.manager.usermerge.action'),
      $container->get('plugin.manager.usermerge.property')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'usermerge_merge_accounts';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $property = $this->reviewSwitcher->getPropertyFromForm(static::class);

    $form['properties']['list'] = [
      '#theme' => 'item_list',
      '#items' => $this->getSupportedProperties(),
      '#title' => $this->t('Supported actions'),
    ];

    $form['general']['retire'] = [
      '#type' => 'entity_autocomplete',
      '#target_type' => 'user',
      '#selection_settings' => [
        'include_anonymous' => FALSE,
      ],
      '#title' => $this->t('The name of the account you wish to retire'),
      '#required' => TRUE,
      '#default_value' => $this->multiStepStorage->getRetiredAccount(),
    ];

    $form['general']['retain'] = [
      '#type' => 'entity_autocomplete',
      '#target_type' => 'user',
      '#selection_settings' => [
        'include_anonymous' => FALSE,
      ],
      '#title' => $this->t('The name of the account you wish to keep'),
      '#required' => TRUE,
      '#default_value' => $this->multiStepStorage->getRetainedAccount(),
    ];

    $default_action = $this->multiStepStorage->getValueFromStore($property, 'action');
    if (is_null($default_action)) {
      $default_action = 'action_block';
    }
    $form['general']['action'] = [
      '#type' => 'select',
      '#title' => $this->t('Action to perform on the account you wish to retire'),
      '#options' => $this->getActionOptions(),
      '#default_value' => $default_action,
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $retire = $form_state->getValue('retire');
    $retain = $form_state->getValue('retain');

    if ($retire == 1) {
      $form_state->setErrorByName('retire', $this->t("Retiring user 1 is not allowed."));
    }

    if ($retire == $retain) {
      $form_state->setErrorByName('retire', $this->t("You must pick a different account from the one you're retiring."));
    }
  }

  /**
   * Get action options which will be displayed in the form.
   *
   * @return array
   *   Action options.
   */
  private function getActionOptions(): array {
    $options = [];

    $definitions = $this->actionPluginManager->getDefinitions();
    foreach ($definitions as $definition) {
      $options[$definition['id']] = (string) $definition['name'];
    }

    return $options;
  }

  /**
   * Get list of enabled properties.
   *
   * @return array
   *   List of properties.
   */
  private function getSupportedProperties(): array {
    $list = [];

    $definitions = $this->propertyPluginManager->getDefinitions();
    foreach ($definitions as $definition) {
      $list[] = $definition['description'];
    }

    return $list;
  }

}
