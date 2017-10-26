<?php
/**
 * This file contains the Task class.
 */

namespace Models;

use Core\Database as DB;
use Core\Request as Request;
use Exception;

/**
 * Create, read, update and delete (or CRUD for short) tasks with this class.
 * You can add a title, description, assignee and one or more files to a task.
 */
class Task
{
    /**
     * Id of the task.
     *
     * @var int
     */
    private static $taskId;

    /**
     * An array holding the task info.
     *
     * @var array
     */
    private static $task = [];

    /**
     * Description of the task.
     *
     * @var string
     */
    private $description;

    /**
     * User id assigned to the task.
     *
     * @var int
     */
    private $assignee;

    /**
     * Array of files used by the task.
     *
     * @var array
     */
    private $files;

    /**
     * Get the task from the tasks table by id.
     *
     * Get task from database.
     *
     * @param int $id
     * @return mixed
     */
    public static function get($id = null)
    {
        if ($id !== null) {
            self::$taskId = $id;
            $query        = "SELECT * FROM tasks
                             WHERE id=:id";
            $stmt         = DB::prepare($query);

            $stmt->bindParam(':id', $id);
            $stmt->execute();

            self::$task = $stmt->fetch();
            if (!empty(self::$task['assignee']))
                self::$task['assignee'] = User::byId(self::$task['assignee']);

            return new self;
        } else {
            if (!empty(self::$task)) {
                if (!empty(self::$task['assignee']))
                    self::$task['assignee'] = self::getAssignee()->getAll();

                return self::$task;
            }

            throw new Exception("Task id cannot be NULL");
        }
    }

    /**
     * Add a new task with the given title.
     *
     * Add a new task with a title. Other columns can be set manually.
     *
     * @param string $title
     * @return self
     */
    public static function add(string $title): self
    {
        $id    = randomId();
        $query = "INSERT INTO tasks (id, title, created_by)
                  VALUES (:id, :title, :created_by)";

        try {
            // Create a PDO instance for later use.
            $pdo  = DB::connect();
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':title', $title);
            $stmt->bindParam(':created_by', $_SESSION['id']);

            if ($stmt->execute()) {
                // Get id of newly created row (use the PDO instance).
                $newId    = $pdo->lastInsertId();
                $getQuery = "SELECT * FROM tasks
                             WHERE private_id=:id";
                $getStmt  = DB::prepare($getQuery);

                $getStmt->bindParam(':id', $newId);
                $getStmt->execute();
                self::$task = $getStmt->fetch();

                return new self;
            }
        } catch(PDOException $e) {}
    }

    /**
     * Get all rows from tasks table.
     *
     * Get all rows. No matter if they're finished or not.
     *
     * @param int $userId
     * @return array
     */
    public static function getAll(int $userId): array
    {
        $query = "SELECT * FROM tasks (created_by=:user_id OR assignee=:assignee_id) ORDER BY private_id DESC";
        $stmt  = DB::prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':assignee_id', $userId);
        $stmt->execute();

        $data = $stmt->fetchAll();
        $data = self::getAllAssignees($data);

        return $data;
    }

    /**
     * Get all todos from tasks table.
     *
     * @param int $userId
     * @return array
     */
    public static function getAllTodos(int $userId): array
    {
        $query = "SELECT * FROM tasks WHERE is_done=false AND (created_by=:user_id OR assignee=:assignee_id) ORDER BY private_id DESC";
        $stmt  = DB::prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':assignee_id', $userId);
        $stmt->execute();

        $data = $stmt->fetchAll();
        $data = self::getAllAssignees($data);

        return $data;
    }

    /**
     * Get all finished todos from tasks table.
     *
     * @param int $userId
     * @return array
     */
    public static function getAllDone(int $userId): array
    {
        $query = "SELECT * FROM tasks WHERE is_done=true AND (created_by=:user_id OR assignee=:assignee_id) ORDER BY private_id DESC";
        $stmt  = DB::prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':assignee_id', $userId);
        $stmt->execute();

        $data = $stmt->fetchAll();
        $data = self::getAllAssignees($data);

        return $data;
    }

    /**
     * Get all assignees by id.
     *
     * Loop through a set of data from the tasks table
     * and extend the assignee column where necessary.
     *
     * @param array $data
     * @return array The extended data.
     */
    private function getAllAssignees(array $data): array
    {
        foreach ($data as &$row) {
            if (!empty($row['assignee'])) {
                $row['assignee'] = User::byId($row['assignee'])->getAll();
            }
        }

        return $data;
    }

    /**
     * Delete a row from the tasks table.
     *
     * @param int $id
     * @return bool
     */
    public static function delete(int $id): bool
    {
        $query = "DELETE FROM tasks WHERE id=:id";
        $stmt  = DB::prepare($query);
        $stmt->bindParam(':id', $id);

        return $stmt->execute();
    }

    /**
     * Check if task is done.
     *
     * @return bool
     */
    public function isDone(): bool
    {
        return self::$task['is_done'] ? true : false;
    }

    /**
     * Sets task to done in the tasks table.
     *
     * @param bool $isDone True if the task is finished.
     * @return self
     */
    public function setDone(bool $isDone): self
    {
        $isDone = $isDone ? 1 : 0;
        $query  = "UPDATE tasks SET is_done=:is_done WHERE id=:id";
        $stmt   = DB::prepare($query);

        $stmt->bindParam(':is_done', $isDone);
        $stmt->bindParam(':id', self::$task['id']);
        $stmt->execute();

        return new self;
    }

    /**
     * Get tasks assignee.
     *
     * @return User
     */
    public function getAssignee(): User
    {
        return self::$task['assignee'];
    }

    /**
     * Set task assignee with User object.
     *
     * @param int $userId
     * @return self
     */
    public function setAssignee(int $userId): self
    {
        $query  = "UPDATE tasks SET assignee=:user_id WHERE id=:id";
        $stmt   = DB::prepare($query);

        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':id', self::$task['id']);
        $stmt->execute();

        // Update task with new assignee id
        self::$task['assignee'] = $userId;

        return new self;
    }

    /**
     * Get the title of the task.
     *
     * @return string
     */
    public function getTitle(): string
    {
        return self::$task['title'];
    }

    /**
     * Set the title of the task.
     *
     * @param string $title
     * @return self
     */
    public function setTitle(string $title): self
    {
        $query = "UPDATE tasks SET title=:title WHERE id=:id";
        $stmt  = DB::prepare($query);

        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':id', self::$task['id']);
        $stmt->execute();

        return new self;
    }

    /**
     * Get the description of the task.
     *
     * @return string
     */
    public function getDescription(): string
    {
        return self::$task['description'];
    }

    /**
     * Set the description of the task.
     *
     * @param string $description
     * @return self
     */
    public function setDescription(string $description): self
    {
        $query = "UPDATE tasks SET description=:description WHERE id=:id";
        $stmt  = DB::prepare($query);

        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':id', self::$task['id']);
        $stmt->execute();

        return new self;
    }

    /**
     * Get an array with File objects.
     *
     * @return array
     */
    public function getFiles(): array
    {
        $query = "SELECT id FROM files WHERE task_id=:task_id";
        $stmt  = DB::prepare($query);

        $stmt->bindParam(':task_id', self::$task['id']);
        $stmt->execute();

        foreach ($stmt->fetchAll() as $file) {
            $files[] = new File($file['id']);
        }

        // Check if array is empty or not.
        return !empty($files) ? $files : [];
    }

    /**
     * Adds the task id to the file in the table.
     *
     * @param File $file
     * @return bool
     */
    public function addFile(File $file): bool
    {
        $query = "UPDATE files SET task_id=:task_id WHERE id=:file_id";
        $stmt  = DB::prepare($query);

        $stmt->bindParam(':task_id', self::$task['id']);
        $stmt->bindParam(':file_id', $file->getId());
        $stmt->execute();

        return ($stmt->rowCount() > 0);
    }

    /**
     * Remove a file from the tables files.
     *
     * @param File $file
     * @return bool
     */
    public function removeFile(File $file): bool
    {
        // Remove from array.
        foreach (self::$task['files'] as $taskFile) {
            if ($taskFile->getId() === $file->getId()) {
                unset($taskFile);
            }
        }

        // Remove row from database.
        return $file->delete();
    }
}
