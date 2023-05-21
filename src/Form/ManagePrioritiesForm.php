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
 * Provides a Priorities Form.
 */
class ManagePrioritiesForm extends FormBase implements ContainerInjectionInterface {

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
      $container->get('current_user'),

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
    return 'todo_app_manage_priorities';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form['add_priority']['name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Priority'),
      '#required' => TRUE,
    ];

    $form['add_priority']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Add'),
    ];

    $rows = [];
    $priorities = $this->repository->loadAll(TodoAppRepository::PRIORITY);
    foreach ($priorities as $priority) {
      $rows[] = [
        'id' => $priority->id,
        'name' => $priority->name,
        'update_link' => Link::fromTextAndUrl('Update',
          Url::fromRoute('todo_app.update_priorities', [
            'id' => $priority->id,
          ])),
        'delete_link' => Link::fromTextAndUrl('Delete',
          Url::fromRoute('todo_app.delete_priorities_confirm', [
            'id' => $priority->id,
          ])),
      ];
    }

    $form['priorities'] = [
      '#type' => 'details',
      '#title' => t('Priorities'),
      '#collapsible' => TRUE,
      '#attributes' => ['open' => ['']],
    ];

    $header = [
      'id' => t('Priority ID'),
      'name' => t('Priority Name'),
      'update' => t('Update'),
      'delete' => t('Delete'),
    ];

    $form['priorities']['priorities_table'] = [
      '#type' => 'table',
      '#header' => $header,
      '#rows' => $rows,
      '#empty' => 'There are no currently registered priorities.',
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $entry = [
      'name' => $form_state->getValue('name'),
    ];
    $this->repository->insert($entry, TodoAppRepository::PRIORITY);
    $form_state->setRedirect('todo_app.manage_priorities');
  }

}
