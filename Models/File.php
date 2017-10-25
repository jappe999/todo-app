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

            $file          = $stmt->fetch();
            $this->id      = $file['id'];
            $this->name    = $file['name'];
            $this->content = $file['content'];
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

        $stmt->bindParam('name', $file['name']);
        $stmt->bindParam('task_id', $taskId);
        $stmt->bindParam('content', $file['content']);

        if ($stmt->execute())
            return $pdo->lastInsertId();
        else
            return 0;
    }

    /**
     * Get all columns that are retrieved from the files table.
     *
     * Get all columns that are retrieved from the files table
     * and are set in the __construct or addNew method.
     */
    public function getAll(): array
    {
      $id      = $this->getId();
      $name    = $this->getName();
      $content = $this->getContent();

      return compact('id', 'name', 'content');
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

    /**
     * Get the the name of the file.
     *
     * @return string
     */
    public function getName(): string
    {
        // Failsafe if someone uses only digits.
        return (string) $this->name;
    }

    /**
     * Set the name of the file.
     *
     * @param string $name
     */
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
     * The content that is retrieved with this function will always be a base64 string.
     * To use the content the string needs to be decoded.
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
    public function setContent($content): bool
    {
        $this->content = $content;

        // Check if base64 encoded
        if (!$this->isBase64($content))
            $this->content = base64_encode($content);

        $query         = "UPDATE files SET content = :content";
        $stmt          = DB::prepare($query);

        $stmt->bindParam(':content', $content);

        return $stmt->execute();
    }

    /**
     * Removes the file from the files table.
     *
     * @return bool
     */
    public function delete(): bool
    {
        $query = "DELETE FROM files WHERE id=:file_id";
        $stmt  = DB::prepare($query);

        $stmt->bind(':file_id', $this->id);

        return $stmt->execute();
    }

    /**
     * Checks if content is base64 encoded.
     *
     * @param string $content
     * @return bool
     */
    public function isBase64(string $content): bool
    {
        return base64_decode($content, true) !== false;
    }

    /**
     * Checks if the type of the file is 'string' and not binary content.
     *
     * @param mixed $content
     * @return bool
     */
    public function isText($content): bool
    {
        return (gettype($content) === 'string' && !$this->isBinary($content));
    }

    /**
     * Check if the content contains valid UTF-8 characters.
     *
     * If the encoding of one (or more) out of all characters isn't UTF-8,
     * it'll return true.
     *
     * @param mixed $content
     * @return bool
     */
    public function isBinary($content): bool
    {
        return !mb_check_encoding($content, 'UTF-8');
    }
}
