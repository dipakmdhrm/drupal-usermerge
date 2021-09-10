<?php

namespace Drupal\usermerge\Form;

use Drupal\Core\Form\FormStateInterface;

/**
 * Class MergeAccountsForm.
 *
 * @package Drupal\usermerge\Form
 */
class ReviewUserForm extends MultiStepFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'usermerge_review_user';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $retire = $this->multiStepStorage->getRetiredAccount();
    $retain = $this->multiStepStorage->getRetainedAccount();

    $form['data'] = [];

    $form['data']['core'] = [
      '#type' => 'table',
      '#header' => [
        $this->t('Property'),
        $this->t('Retired account'),
        $this->t('Retained account'),
      ],
    ];

    $row = 1;
    $discarded = [
      'uid' => 'id',
      'uuid' => 'uuid',
      'name' => 'getAccountName',
      'email' => 'getEmail',
    ];
    foreach ($discarded as $property => $method) {
      $form['data']['core'][$row]['property'] = [
        '#markup' => $property,
      ];

      $form['data']['core'][$row]['retire'] = [
        '#markup' => $retire->{$method}(),
      ];

      $form['data']['core'][$row]['retain'] = [
        '#markup' => $retain->{$method}(),
      ];
      $row++;
    }

    // Add submit buttons.
    $form = parent::buildForm($form, $form_state);

    return $form;
  }

}
