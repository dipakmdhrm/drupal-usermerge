<?php

namespace Drupal\usermerge\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Drupal\usermerge\ReviewFormSwitcherInterface;
use Drupal\usermerge\Exception\UserMergeException;

/**
 * Class MultiStepFormController.
 *
 * @package Drupal\usermerge\Controller
 */
class MultiStepFormController extends ControllerBase {

  /**
   * Review form switcher.
   *
   * @var \Drupal\usermerge\ReviewFormSwitcherInterface
   */
  protected $reviewSwitcher;

  /**
   * MergeAccountsForm constructor.
   *
   * @param \Drupal\usermerge\ReviewFormSwitcherInterface $review_switcher
   *   Review form steps.
   */
  public function __construct(ReviewFormSwitcherInterface $review_switcher) {
    $this->reviewSwitcher = $review_switcher;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('usermerge.review_form_switcher')
    );
  }

  /**
   * Display the multi step form.
   *
   * @param string $property
   *   Id of the property plugin.
   *
   * @return array
   *   Return markup array.
   *
   * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
   */
  public function content($property = '') {
    try {
      $form_name = $this->reviewSwitcher->getFormFromProperty($property);
    }
    catch (UserMergeException $e) {
      throw new NotFoundHttpException();
    }

    $form = $this->formBuilder()
      ->getForm($form_name);

    return $form;
  }

}
