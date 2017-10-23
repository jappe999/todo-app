<?php

namespace Models;

use Core\Database as DB;

class File
{
    /**
     * Corresponding id of the file in the database.
     *
     * @var int
     */
    private $id;

    /**
     * Name of the file.
     *
     * @var string
     */
    private $name;

    /**
     * Content of the file. Can be text or binary content.
     *
     * @var string
     */
    private $content;

    /**
     * Construct the File object with or without predefined content.
     *
     * Construct the File object.
     * If $id is given, $this->content will be set
     * to the corresponding row from the table files.
     */
    function __construct($id = null)
    {
        if (!empty($id)) {
            $query = "SELECT * FROM files WHERE id=:file_id";
            $stmt  = DB::prepare($query);

            $stmt->bindParam(':file_id', $id);
            $stmt->execute();

            $file       = $stmt->fetch();
            $this->id   = $file['id'];
            $this->name = $file['name'];

            // Checks which column is used.
            if (!empty($file['content']))
                $this->content = $file['content'];
            else
                $this->content = $file['content_binary'];
        }
    }

    /**
     * Adds a new file and connects it to the task.
     *
     * Adds a new file to the files table and sets the tasks id.
     */
    function addNew(array $file, int $taskId): int
    {
        $query = "INSERT INTO files
                  (name, task_id, content)
                  VALUES (:name, :task_id, :content)";
        $pdo   = DB::connect();
        $stmt  = $pdo->prepare($query);

        var_dump($file);

        $stmt->bindParam('name', $file['name']);
        $stmt->bindParam('task_id', $taskId);
        $stmt->bindParam('content', $file['content']);

        if ($stmt->execute())
            return $pdo->lastInsertId();
        else
            return 0;
    }

    /**
     * Get the id of the row in the tables files corresponding to the file.
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name)
    {
        $query = "UPDATE files SET name = :name";

        $stmt = DB::prepare($query);
        $stmt->bindParam(':name', $name);
        $stmt->execute();

        $this->name = $name;
    }

    /**
     * Returns the content set in the __construct or setContent.
     *
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * Put the given content in the database.
     *
     * Checks if content is text or binary and will put the content
     * in the corresponding column in the files table.
     * The other column will be emptied.
     *
     * @param string $content
     */
    public function setContent($content)
    {
        // Must be set for isText and isBinary to work.
        $this->content = $content;

        if ($this->isText()) {
            $queries = [
                "UPDATE files SET content = :content",
                "UPDATE files SET content_binary = ''",
            ];
        } elseif ($this->isBinary()) {
            $queries = [
                "UPDATE files SET content_binary = :content",
                "UPDATE files SET content = ''",
            ];
        }

        foreach ($queries as $query) {
            $stmt = DB::prepare($query);
            $stmt->bindParam(':content', $content);
            $stmt->execute();
        }
    }

    /**
     * Removes the file form the files tables.
     */
    public function delete()
    {
        $query = "DELETE FROM files WHERE id=:file_id";

        $stmt = DB::prepare($query);
        $stmt->bind(':file_id', $this->id);
        $stmt->execute();
    }

    /**
     * Checks if the type of the file is 'string' and not binary content.
     *
     * @return bool
     */
    public function isText(): bool
    {
        return (gettype($this->content) === 'string' && !$this->isBinary());
    }

    /**
     * Check if the content contains valid UTF-8 characters.
     *
     * If the encoding of one (or more) out of all characters isn't UTF-8,
     * it'll return true.
     *
     * @return bool
     */
    public function isBinary(): bool
    {
        return !mb_check_encoding($this->content, 'UTF-8');
    }
}
