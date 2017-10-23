<?php

namespace Models;

use Core\Database as DB;
use Core\Request as Request;
use Exception;

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
    private static $task = array();

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

            self::$task          = $stmt->fetch();

            return new self;
        } else {
            if (!empty(self::$task))
                return self::$task;

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
        $query = "INSERT INTO tasks (title)
                  VALUES (:title)";

        try {
            // Create a PDO instance for later use.
            $pdo  = DB::connect();
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':title', $title);

            if ($stmt->execute()) {
                // Get id of newly created row (use the PDO instance).
                $newId    = $pdo->lastInsertId();
                $getQuery = "SELECT * FROM tasks
                             WHERE id=:id";
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
     * @return array
     */
    public static function getAll(): array
    {
        $query = "SELECT * FROM tasks ORDER BY id DESC";
        $stmt  = DB::prepare($query);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Get all todos from tasks table.
     *
     * @return array
     */
    public static function getAllTodos(): array
    {
        $query = "SELECT * FROM tasks WHERE is_done=false ORDER BY id DESC";
        $stmt  = DB::prepare($query);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Get all finished todos from tasks table.
     *
     * @return array
     */
    public static function getAllDone(): array
    {
        $query = "SELECT * FROM tasks WHERE is_done=true ORDER BY id DESC";
        $stmt  = DB::prepare($query);
        $stmt->execute();

        return $stmt->fetchAll();
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
     * @return bool
     */
    public function setDone(bool $isDone): bool
    {
        $isDone = $isDone ? 1 : 0;
        $query  = "UPDATE tasks SET is_done=:is_done WHERE id=:id";
        $stmt   = DB::prepare($query);

        $stmt->bindParam(':is_done', $isDone);
        $stmt->bindParam(':id', self::$task['id']);

        return $stmt->execute();
    }

    /**
     * Get tasks assignee id.
     *
     * @return User
     */
    public function getAssignee()
    {
        return User::get(self::$task['assignee']);
    }

    /**
     * Set task assignee with User object.
     *
     * @param object $object
     * @return bool
     */
    public function setAssignee($user): bool
    {
        $userId = $user->getId();
        $query  = "UPDATE tasks SET assignee=:user_id WHERE id=:id";
        $stmt   = DB::prepare($query);

        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':id', self::$task['id']);

        if ($stmt->execute()) {
            // Update task with new assignee id
            self::$task['assignee'] = $userId;
            return true;
        }

        return false;

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
     * @return bool
     */
    public function setTitle(string $title): bool
    {
        $query = "UPDATE tasks SET title=:title WHERE id=:id";
        $stmt  = DB::prepare($query);

        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':id', self::$task['id']);

        return $stmt->execute();
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
     * @return bool
     */
    public function setDescription(string $description): bool
    {
        $query = "UPDATE tasks SET description=:description WHERE id=:id";
        $stmt  = DB::prepare($query);

        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':id', self::$task['id']);

        return $stmt->execute();
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
