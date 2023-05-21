<?php

namespace Drupal\todo_app\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\todo_app\TodoAppRepository;

/**
 * Categories Update Form.
 */
class CategoriesUpdateForm extends TodoFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'categories_update_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $name = TodoAppRepository::CATEGORY) {
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
    $result = $this->repository->update($entry, TodoAppRepository::CATEGORY);
    if ($result) {
      $this->messenger()->addMessage($this->t('Updated category.'));
      $form_state->setRedirect('todo_app.manage_categories');
    }
  }

}
