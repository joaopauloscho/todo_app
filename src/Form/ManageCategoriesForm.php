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
 * Provides a Categories Form.
 */
class ManageCategoriesForm extends FormBase implements ContainerInjectionInterface {

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
    return 'todo_app_manage_categories';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form['add_category']['name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Category'),
      '#required' => TRUE,
    ];

    $form['add_category']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Add'),
    ];

    $rows = [];
    $categories = $this->repository->loadAll(TodoAppRepository::CATEGORY);
    foreach ($categories as $category) {
      $rows[] = [
        'id' => $category->id,
        'name' => $category->name,
        'update_link' => Link::fromTextAndUrl('Update',
          Url::fromRoute('todo_app.update_categories', [
            'id' => $category->id,
          ])),
        'delete_link' => Link::fromTextAndUrl('Delete',
          Url::fromRoute('todo_app.delete_categories_confirm', [
            'id' => $category->id,
          ])),
      ];
    }

    $form['categories'] = [
      '#type' => 'details',
      '#title' => t('Categories'),
      '#collapsible' => TRUE,
      '#attributes' => ['open' => ['']],
    ];

    $header = [
      'id' => t('Category ID'),
      'name' => t('Category Name'),
      'update' => t('Update'),
      'delete' => t('Delete'),
    ];

    $form['categories']['categories_table'] = [
      '#type' => 'table',
      '#header' => $header,
      '#rows' => $rows,
      '#empty' => 'There are no currently registered categories.',
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
    $this->repository->insert($entry, TodoAppRepository::CATEGORY);
    $form_state->setRedirect('todo_app.manage_categories');
  }

}
