<?php

namespace Drupal\todo_app;

use Drupal\Core\Database\Connection;
use Drupal\Core\Messenger\MessengerInterface;

/**
 * Repository for database-related helper methods for to do app.
 */
class TodoAppRepository {

  /**
   * The category table.
   */
  const CATEGORY = 'category';

  /**
   * The priority table.
   */
  const PRIORITY = 'priority';

  /**
   * The status table.
   */
  const STATUS = 'status';

  /**
   * The task table.
   */
  const TASK = 'task';

  /**
   * The database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected Connection $connection;

  /**
   * The messenger.
   *
   * @var \Drupal\Core\Messenger\MessengerInterface
   */
  protected MessengerInterface $messenger;

  /**
   * Constructs a TodoAppRepository object.
   *
   * @param \Drupal\Core\Database\Connection $connection
   *   The database connection.
   * @param \Drupal\Core\Messenger\MessengerInterface $messenger
   *   The messenger.
   */
  public function __construct(Connection $connection, MessengerInterface $messenger) {
    $this->connection = $connection;
    $this->messenger = $messenger;
  }

  /**
   * Save an entry to the database.
   *
   * @param array $entry
   *   The entry.
   * @param string $table
   *   The table.
   *
   * @return int|null
   *   TRUE if inserted, NULL otherwise.
   */
  public function insert(array $entry, string $table): ?int {
    try {
      $return_value = $this->connection->insert($table)
        ->fields($entry)
        ->execute();
    }
    catch (\Exception $e) {
      $this->messenger->addMessage('Insert failed. Message = %message', [
        '%message' => $e->getMessage(),
      ], 'error');
    }
    return $return_value ?? NULL;
  }

  /**
   * Update record to the database.
   *
   * @param array $entry
   *   The entry.
   * @param string $table
   *   The table.
   *
   * @return int|null
   *   TRUE if updated, NULL otherwise.
   */
  public function update(array $entry, string $table): ?int {
    try {
      $return_value = $this->connection->update($table)
        ->fields($entry)
        ->condition('id', $entry['id'])
        ->execute();
    }
    catch (\Exception $e) {
      $this->messenger->addMessage('Update failed. Message = %message', [
        '%message' => $e->getMessage(),
      ], 'error');
    }
    return $return_value ?? NULL;
  }

  /**
   * Delete an entry from the database.
   *
   * @param $id
   *   The entry id.
   * @param string $table
   *   The table.
   *
   * @return bool
   *   TRUE if deleted, FALSE otherwise.
   */
  public function delete(string $id, string $table): bool {
    return $this->connection->delete($table)
      ->condition('id', $id)
      ->execute();
  }

  /**
   * Query all the records of a table.
   *
   * @param string $table
   *   The table to query.
   *
   * @return array|null
   *   An array containing the loaded entries if found.
   */
  public function loadAll(string $table): ?array {
    try {
      return $this->connection
        ->select($table, 't')
        ->fields('t')
        ->execute()
        ->fetchAll();
    }
    catch (\Exception $e) {
      $this->messenger->addMessage('Load failed. Message = %message', [
        '%message' => $e->getMessage(),
      ], 'error');

      return NULL;
    }
  }

  /**
   * Load a entry by the id.
   *
   * @param $id
   *   The entry id.
   * @param string $table
   *   The table to query.
   *
   * @return array|null
   *   An array containing the loaded entry if found.
   */
  public function loadById($id, string $table): ?array {
    try {
      if ($id == '0') {
        return NULL;
      }

      return $this->connection
        ->select($table, 't')
        ->fields('t')
        ->condition('id', $id)
        ->execute()
        ->fetchAssoc();
    }
    catch (\Exception $e) {
      $this->messenger->addMessage('Load failed. Message = %message', [
        '%message' => $e->getMessage(),
      ], 'error');

      return NULL;
    }
  }

  /**
   * Query list of tasks by user and order by the closest due_date.
   *
   * @param string $uid
   *   The user id.
   *
   * @return array|null
   *   An array containing the loaded entries if found.
   */
  public function loadTasksByUser(string $uid): ?array {
    try {
      $due_date = $this->connection
        ->select(self::TASK, 't')
        ->fields('t')
        ->condition('uid', $uid)
        ->condition('due_date', '0', '<>')
        ->orderBy('due_date', 'ASC')
        ->execute()->fetchAll();

      $no_due_date = $this->connection
        ->select(self::TASK, 't')
        ->fields('t')
        ->condition('uid', $uid)
        ->condition('due_date', '0', '=')
        ->execute()->fetchAll();

      return array_merge($due_date, $no_due_date);
    }
    catch (\Exception $e) {
      $this->messenger->addMessage('Load failed. Message = %message', [
        '%message' => $e->getMessage(),
      ], 'error');

      return NULL;
    }
  }

  /**
   * Get the list of options for the forms.
   *
   * @param $table
   *   The table.
   * @return array
   *   The array of options.
   */
  public function getOptions($table): array {
    $options = [];
    $entries = $this->loadAll($table);
    foreach ($entries as $entry) {
      $options[$entry->id] = $entry->name;
    }

    return $options;
  }

}
