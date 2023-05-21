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
 * Add Tasks Form.
 */
class AddTasksForm extends FormBase implements ContainerInjectionInterface {

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
   * Construct the new form object.
   *
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
    return 'todo_app_add';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['add_task']['name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Task'),
      '#required' => TRUE,
      '#size' => 30,
    ];

    $form['add_task']['description'] = [
      '#type' => 'text_format',
      '#format' => 'full_html',
      '#title' => $this->t('Description'),
    ];

    $form['add_task']['category'] = [
      '#type' => 'select',
      '#options' => $this->repository->getOptions(TodoAppRepository::CATEGORY),
      '#title' => $this->t('Category'),
      '#empty_option' => $this->t('- Select the category -'),
    ];

    $form['add_task']['priority'] = [
      '#type' => 'select',
      '#options' => $this->repository->getOptions(TodoAppRepository::PRIORITY),
      '#title' => $this->t('Priority'),
      '#empty_option' => $this->t('- Select the priority -'),
    ];

    $form['add_task']['status'] = [
      '#type' => 'select',
      '#options' => $this->repository->getOptions(TodoAppRepository::STATUS),
      '#title' => $this->t('Status'),
      '#empty_option' => $this->t('- Select the status -'),
    ];

    $form['add_task']['created'] = [
      '#type' => 'date',
      '#title' => $this->t('Created'),
      '#required' => TRUE,
      '#access' => FALSE,
    ];

    $form['add_task']['due_date'] = [
      '#type' => 'date',
      '#title' => $this->t('Due Date'),
    ];

    $form['add_task']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Add'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $account = $this->currentUser();
    $entry = [
      'uid' => $account->id(),
      'name' => $form_state->getValue('name'),
      'description' => $form_state->getValue('description')['value'],
      'cid' => $form_state->getValue('category'),
      'pid' => $form_state->getValue('priority'),
      'sid' => $form_state->getValue('status'),
      'created' => (new \DateTime('now'))->getTimestamp(),
      'due_date' => strtotime($form_state->getValue('due_date')),
    ];
    $result = $this->repository->insert(array_filter($entry), TodoAppRepository::TASK);
    if ($result) {
      $this->messenger()->addMessage($this->t('Created Task.'));
      $form_state->setRedirect('todo_app.manage_tasks');
    }
  }

}
