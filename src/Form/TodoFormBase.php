<?php

namespace Drupal\todo_app\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\todo_app\TodoAppRepository;
use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class TodoFormBase extends FormBase {

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
  public function buildForm(array $form, FormStateInterface $form_state, $name = NULL) {
    // Wrap the form in a div.
    $form = [
      '#prefix' => '<div id="updateform">',
      '#suffix' => '</div>',
    ];

    // Query for items to display.
    $entries = $this->repository->loadAll($name);
    // Tell the user if there is nothing to display.
    if (empty($entries)) {
      $form['no_values'] = [
        '#value' => $this->t('No entries exist in the table.'),
      ];
      return $form;
    }

    $keyed_entries = [];
    $options = [];
    foreach ($entries as $entry) {
      $options[$entry->id] = $this->t('@id: @name', [
        '@id' => $entry->id,
        '@name' => $entry->name,

      ]);
      $keyed_entries[$entry->id] = $entry;
    }

    $id = $form_state->getValue('id');

    $default_entry = !empty($id) ? $keyed_entries[$id] : $entries[0];

    $form_state->setValue('entries', $keyed_entries);

    $form['id'] = [
      '#type' => 'select',
      '#options' => $options,
      '#title' => $this->t('Choose entry to update'),
      '#default_value' => $default_entry->id,
      '#ajax' => [
        'wrapper' => 'updateform',
        'callback' => [$this, 'updateCallback'],
      ],
    ];

    $form['name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Updated name'),
      '#size' => 15,
      '#default_value' => $default_entry->name,
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Update'),
    ];

    return $form;
  }

  /**
   * AJAX callback handler.
   */
  public function updateCallback(array $form, FormStateInterface $form_state) {
    $entries = $form_state->getValue('entries');
    $entry = $entries[$form_state->getValue('id')];
    $form['name']['#value'] = $entry->name;

    return $form;
  }

}
