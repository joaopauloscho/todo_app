<?php

namespace Drupal\todo_app\Form;

use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\todo_app\TodoAppRepository;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Delete priorities.
 */
class DeleteStatusesConfirmForm extends ConfirmFormBase {

  /**
   * The database repository service.
   *
   * @var \Drupal\todo_app\TodoAppRepository
   */
  protected TodoAppRepository $repository;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('todo_app.repository'),
    );
  }

  /**
   * @param TodoAppRepository $repository
   */
  public function __construct(TodoAppRepository $repository) {
    $this->repository = $repository;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'todo_app_delete_statuses_confirm';
  }

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    return $this->t('Are you sure you want to do this?');
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelUrl() {
    return new Url('todo_app.manage_statuses');
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $id = \Drupal::request()->get('id');
    if ($this->repository->delete($id, TodoAppRepository::STATUS)) {
      $this->messenger()->addStatus($this->t('Status deleted!'));
      $form_state->setRedirectUrl(new Url('todo_app.manage_statuses'));
    }
    else {
      $this->messenger()->addError($this->t('Could not delete status!'));
    }
  }
}
