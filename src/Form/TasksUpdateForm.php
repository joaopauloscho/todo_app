<?php

namespace Drupal\todo_app\Form;

use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\todo_app\TodoAppRepository;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Tasks Update Form.
 */
class TasksUpdateForm extends FormBase {

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
    $form = new static($container->get('todo_app.repository'));
    $form->setMessenger($container->get('messenger'));
    return $form;
  }

  /**
   * Construct the new form object.
   */
  public function __construct(TodoAppRepository $repository) {
    $this->repository = $repository;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'tasks_update_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $id = \Drupal::request()->get('id');
    $entry = $this->repository->loadById($id, TodoAppRepository::TASK);

    $form['name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Task'),
      '#default_value' => $entry['name'],
    ];

    $form['description'] = [
      '#type' => 'text_format',
      '#format' => 'full_html',
      '#title' => $this->t('Description'),
      '#default_value' => $entry['description'],
    ];

    $form['category'] = [
      '#type' => 'select',
      '#options' => $this->repository->getOptions(TodoAppRepository::CATEGORY),
      '#title' => $this->t('Category'),
      '#default_value' => $entry['cid'],
      '#empty_option' => $this->t('- Select the category -'),
    ];

    $form['priority'] = [
      '#type' => 'select',
      '#options' => $this->repository->getOptions(TodoAppRepository::PRIORITY),
      '#title' => $this->t('Priority'),
      '#default_value' => $entry['pid'],
      '#empty_option' => $this->t('- Select the priority -'),
    ];

    $form['status'] = [
      '#type' => 'select',
      '#options' => $this->repository->getOptions(TodoAppRepository::STATUS),
      '#title' => $this->t('Status'),
      '#default_value' => $entry['sid'],
      '#empty_option' => $this->t('- Select the status -'),
    ];

    $form['changed'] = [
      '#type' => 'date',
      '#title' => $this->t('Changed'),
      '#default_value' => $entry['changed'],
      '#access' => FALSE,
    ];

    $form['due_date'] = [
      '#type' => 'date',
      '#title' => $this->t('Due Date'),
      '#default_value' => DrupalDateTime::createFromTimestamp($entry['due_date'])->format('Y-m-d'),
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Save'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $id = \Drupal::request()->get('id');
    $entry = [
      'id' => $id,
      'name' => $form_state->getValue('name'),
      'description' => $form_state->getValue('description')['value'],
      'cid' => $form_state->getValue('category'),
      'pid' => $form_state->getValue('priority'),
      'sid' => $form_state->getValue('status'),
      'changed' => (new \DateTime('now'))->getTimestamp(),
      'due_date' => strtotime($form_state->getValue('due_date')),
    ];
    $result = $this->repository->update(array_filter($entry), TodoAppRepository::TASK);
    if ($result) {
      $this->messenger()->addMessage($this->t('Updated task.'));
      $form_state->setRedirect('todo_app.manage_tasks');
    }
  }

}
