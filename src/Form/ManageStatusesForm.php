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
 * Provides a Statuses Form.
 */
class ManageStatusesForm extends FormBase implements ContainerInjectionInterface {

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
    return 'todo_app_manage_statuses';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form['add_status']['name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Status'),
      '#required' => TRUE,
    ];

    $form['add_status']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Add'),
    ];

    $rows = [];
    $statuses = $this->repository->loadAll(TodoAppRepository::STATUS);
    foreach ($statuses as $status) {
      $rows[] = [
        'id' => $status->id,
        'name' => $status->name,
        'update_link' => Link::fromTextAndUrl('Update',
          Url::fromRoute('todo_app.update_statuses', [
            'id' => $status->id,
          ])),
        'delete_link' => Link::fromTextAndUrl('Delete',
          Url::fromRoute('todo_app.delete_statuses_confirm', [
            'id' => $status->id,
          ])),
      ];
    }

    $form['statuses'] = [
      '#type' => 'details',
      '#title' => t('Statuses'),
      '#collapsible' => TRUE,
      '#attributes' => ['open' => ['']],
    ];

    $header = [
      'id' => t('Status ID'),
      'name' => t('Status Name'),
      'update' => t('Update'),
      'delete' => t('Delete'),
    ];

    $form['statuses']['statuses_table'] = [
      '#type' => 'table',
      '#header' => $header,
      '#rows' => $rows,
      '#empty' => 'There are no currently registered statuses.',
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
    $this->repository->insert($entry, TodoAppRepository::STATUS);
    $form_state->setRedirect('todo_app.manage_statuses');
  }

}
