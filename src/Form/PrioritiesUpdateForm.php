<?php

namespace Drupal\todo_app\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\todo_app\TodoAppRepository;

/**
 * Priorities Update Form.
 */
class PrioritiesUpdateForm extends TodoFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'priorities_update_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $name = TodoAppRepository::PRIORITY) {
    return parent::buildForm($form, $form_state, $name);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Save the submitted entry.
    $entry = [
      'id' => $form_state->getValue('id'),
      'name' => $form_state->getValue('name'),
    ];
    $result = $this->repository->update($entry, TodoAppRepository::PRIORITY);
    if ($result) {
      $this->messenger()->addMessage($this->t('Updated priority.'));
      $form_state->setRedirect('todo_app.manage_priorities');
    }
  }

}
