<?php

namespace Drupal\todo_app\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\todo_app\TodoAppRepository;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Returns responses for To Do App routes.
 */
class TodoAppController extends ControllerBase {

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
    return new static(
      $container->get('todo_app.repository'),
    );
  }

  /**
   * @param TodoAppRepository $repository
   */
  public function __construct(TodoAppRepository $repository) {
    $this->repository = $repository;
    parent::currentUser();
  }

  /**
   * Generates the to do tasks list.
   *
   * @return array
   */
//  public function buildList(): array {
//    $content = [];
//
//    $content['message'] = [
//      '#markup' => $this->t('You to do tasks.'),
//    ];
//
//    $rows = [];
//    $headers = [
//
//      $this->t('Name'),
//      $this->t('Category'),
//      $this->t('Priority'),
//      $this->t('Status'),
//      $this->t('Created'),
//      $this->t('Changed'),
//      $this->t('Due Date'),
//      $this->t('Edit'),
//      $this->t('Delete'),
//
//    ];
//
//    $entries = $this->repository->loadTasksByUser($this->currentUser->id(), TodoAppRepository::TASK);
//
//    foreach ($entries as $entry) {
//      $rows[] = [
//        'name' => $entry->name,
//        'category' => $this->repository->loadById($entry->cid, TodoAppRepository::CATEGORY)['name'],
//        'priority' => $this->repository->loadById($entry->pid,TodoAppRepository::PRIORITY)['name'],
//        'status' => $this->repository->loadById($entry->sid,TodoAppRepository::STATUS)['name'],
//        'created' => date('d-m-Y', $entry->created),
//        'changed' => $entry->changed != '0' ? date('d-m-Y', $entry->changed) : NULL,
//        'due_date' => $entry->due_date != '0' ? date('d-m-Y', $entry->due_date) : NULL,
//        'update_link' => Link::fromTextAndUrl('Update',
//          Url::fromRoute('todo_app.update', [
//            'id' => $entry->id,
//          ])),
//        'delete_link' => Link::fromTextAndUrl('Delete',
//          Url::fromRoute('todo_app.delete_confirm', [
//            'id' => $entry->id,
//          ])),
//      ];
//
//    }
//
//    $content['table'] = [
//      '#type' => 'table',
//      '#header' => $headers,
//      '#rows' => $rows,
//      '#empty' => $this->t('No entries available.'),
//    ];
//
//    // Don't cache this page.
//    $content['#cache']['max-age'] = 0;
//
//    return $content;
//  }

}
