<?php

namespace Drupal\todo_app\Form;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Link;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\Core\Url;
use Drupal\todo_app\TodoAppRepository;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Manage To do Tasks Form.
 */
class ManageTasksForm extends FormBase implements ContainerInjectionInterface {

  /**
   * The database repository service.
   *
   * @var \Drupal\todo_app\TodoAppRepository
   */
  protected TodoAppRepository $repository;

  /**
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected AccountProxyInterface $currentUser;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('todo_app.repository'),
      $container->get('current_user')
    );
  }

  /**
   * @param TodoAppRepository $repository
   * @param AccountProxyInterface $currentUser
   */
  public function __construct(TodoAppRepository $repository, AccountProxyInterface $currentUser) {
    $this->repository = $repository;
    $this->currentUser = $currentUser;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'todo_app_manage_tasks';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $rows = [];

    $header = [

      $this->t('Name'),
      $this->t('Category'),
      $this->t('Priority'),
      $this->t('Status'),
      $this->t('Created'),
      $this->t('Changed'),
      $this->t('Due Date'),
      $this->t('Edit'),
      $this->t('Delete'),

    ];

    $entries = $this->repository->loadTasksByUser($this->currentUser->id());

    foreach ($entries as $entry) {
      $rows[] = [
        'name' => $entry->name,
        'category' => $this->repository->loadById($entry->cid, TodoAppRepository::CATEGORY)['name'],
        'priority' => $this->repository->loadById($entry->pid,TodoAppRepository::PRIORITY)['name'],
        'status' => $this->repository->loadById($entry->sid,TodoAppRepository::STATUS)['name'],
        'created' => date('d-m-Y', $entry->created),
        'changed' => $entry->changed != '0' ? date('d-m-Y', $entry->changed) : NULL,
        'due_date' => $entry->due_date != '0' ? date('d-m-Y', $entry->due_date) : NULL,
        'update_link' => Link::fromTextAndUrl('Update',
          Url::fromRoute('todo_app.update', [
            'id' => $entry->id,
          ])),
        'delete_link' => Link::fromTextAndUrl('Delete',
          Url::fromRoute('todo_app.delete_confirm', [
            'id' => $entry->id,
          ])),
      ];

    }

    $form['tasks'] = [
      '#type' => 'details',
      '#title' => t('My Tasks'),
      '#collapsible' => TRUE,
      '#attributes' => ['open' => ['']],
    ];

    $form['tasks']['tasks_table'] = [
      '#type' => 'table',
      '#header' => $header,
      '#rows' => $rows,
      '#empty' => 'There are no currently registered tasks.',
    ];

    return $form;
  }

  public function submitForm(array &$form, FormStateInterface $form_state)
  {
    // TODO: Implement submitForm() method.
  }
}
