<?php

namespace Drupal\usermerge\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\usermerge\ReviewFormSwitcherInterface;
use Drupal\usermerge\BatchGeneratorInterface;
use Drupal\usermerge\MultiStepStorageInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class MultiStepFormBase.
 *
 * @package Drupal\usermerge\Form
 */
abstract class MultiStepFormBase extends FormBase {

  /**
   * Multi step storage.
   *
   * @var \Drupal\usermerge\MultiStepStorageInterface
   */
  protected $multiStepStorage;

  /**
   * Review form steps switcher.
   *
   * @var \Drupal\usermerge\ReviewFormSwitcherInterface
   */
  protected $reviewSwitcher;

  /**
   * Batch generator.
   *
   * @var \Drupal\usermerge\BatchGeneratorInterface
   */
  protected $batchGenerator;

  /**
   * MultiStepFormBase constructor.
   *
   * @param \Drupal\usermerge\MultiStepStorageInterface $multi_step_storage
   *   Multi step storage.
   * @param \Drupal\usermerge\ReviewFormSwitcherInterface $review_switcher
   *   Review form switcher.
   * @param \Drupal\usermerge\BatchGeneratorInterface $batch_generator
   *   Batch generator.
   */
  public function __construct(MultiStepStorageInterface $multi_step_storage, ReviewFormSwitcherInterface $review_switcher, BatchGeneratorInterface $batch_generator) {
    $this->multiStepStorage = $multi_step_storage;
    $this->reviewSwitcher = $review_switcher;
    $this->batchGenerator = $batch_generator;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('usermerge.multi_step_storage'),
      $container->get('usermerge.review_form_switcher'),
      $container->get('usermerge.batch_generator')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['actions']['#type'] = 'actions';
    if ($this->reviewSwitcher->hasPrevious(static::class)) {
      $form['actions']['previous'] = [
        '#type' => 'submit',
        '#value' => $this->t('Go back'),
        '#button_type' => 'primary',
        '#weight' => 10,
        '#submit' => ['::submitGoBack'],
      ];
    }

    if ($this->reviewSwitcher->hasNext(static::class)) {
      $form['actions']['next'] = [
        '#type' => 'submit',
        '#value' => $this->t('Continue'),
        '#button_type' => 'primary',
        '#weight' => 10,
        '#submit' => ['::submitForm', '::submitGoNext'],
      ];
    }
    else {
      $form['actions']['submit'] = [
        '#type' => 'submit',
        '#value' => $this->t('Merge accounts'),
        '#button_type' => 'primary',
        '#weight' => 10,
        '#submit' => ['::submitForm', '::submitCreateBatch'],
      ];
    }

    return $form;
  }

  /**
   * Form submission for back button.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   */
  public function submitGoBack(array &$form, FormStateInterface $form_state) {
    $previous = $this->reviewSwitcher->getPreviousProperty(static::class);
    $form_state->setRedirect('usermerge.multi_step_form', ['property' => $previous]);
  }

  /**
   * Form submission for next button.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   */
  public function submitGoNext(array &$form, FormStateInterface $form_state) {
    $next = $this->reviewSwitcher->getNextProperty(static::class);
    $form_state->setRedirect('usermerge.multi_step_form', ['property' => $next]);
  }

  /**
   * Form submission for the last step.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   */
  public function submitCreateBatch(array &$form, FormStateInterface $form_state) {
    $this->batchGenerator->createBatch();
    $form_state->setRedirect('usermerge.multi_step_form');
  }

  /**
   * {@inheritdoc}
   *
   * @throws \Drupal\Core\TempStore\TempStoreException
   */
  final public function submitForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();
    $property = $this->reviewSwitcher->getPropertyFromForm(static::class);

    $this->multiStepStorage->setValues($property, $values);

    $form_state->setRedirect('usermerge.multi_step_form', ['property' => 'property_user']);
  }

}
