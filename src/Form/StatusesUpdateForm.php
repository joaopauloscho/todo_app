<?php

namespace Drupal\todo_app\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\todo_app\TodoAppRepository;

/**
 * Statuses Update Form.
 */
class StatusesUpdateForm extends TodoFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'statuses_update_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $name = TodoAppRepository::STATUS) {
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
    $result = $this->repository->update($entry, TodoAppRepository::STATUS);
    if ($result) {
      $this->messenger()->addMessage($this->t('Updated status.'));
      $form_state->setRedirect('todo_app.manage_statuses');
    }
  }

}
