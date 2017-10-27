<?php

namespace Controllers;

use Models\User as User;
use Models\Task as Task;
use Models\File as File;
use Core\Database as DB;
use Core\Request as Request;

/**
 * FilesController
 */
class FilesController extends Controller
{

    /**
     * Adds a new file
     *
     * Adds a new file to the files table.
     * Files are sent as a base64 string with the POST method.
     *
     * @return string
     */
    public function addNew(): string
    {
        if (!Request::isPost()) {
            $status = 'error';
            $error  = 'This route can only be accessed with POST headers';
            return json_encode(compact('status', 'error'));
        }

        $params  = Request::getParams();
        $task    = $params->get('task');
        $file    = $params->get('file');
        $status  = 'success';

        // Actual processing of the file.
        $newFile = new File();
        $fileId  = $newFile->addNew($file, $task['id']);

        if ($fileId !== 0) {
            $data = $fileId;
        } else {
            $status = 'error';
            $error  = 'File ' . $file['name'] . ' could not be uploaded.';
            return json_encode(compact('status', 'error'));
        }

        return json_encode(compact('status', 'data'));
    }

    /**
     * Get a file from the files table based on an id.
     *
     * Get the decoded base64 string from the files table corresponding
     * to the given file id.
     *
     * @param int $fileId
     * @return mixed
     */
    public function getFile(int $fileId)
    {
        $query = "SELECT content FROM files
                  WHERE id=:id";
        $stmt  = DB::prepare($query);

        $stmt->bindParam(':id', $fileId);
        $stmt->execute();

        $content = $stmt->fetch();

        // Check if content exists.
        if ($content) $content = $content['content'];
        else return error('404');

        // Remove dumb data.
        $content = substr($content, 5);

        // Check if user has the rights to see this file.
        $file = new File($fileId);
        if (!$this->hasRights($file)) {
            return error('403');
        }

        // Strip base64 content and set header.
        $content = preg_replace_callback(
                       '/^([a-z0-9\-\_]+\/[a-z0-9\-\_]+)(;base64,){1}/',
                       function($matches) {
                           header("Content-Type:" . $matches[1]);
                           return '';
                       },
                       $content
                   );

        return base64_decode($content);
    }

    /**
     * Updates a file in the database.
     *
     * Updates a file in the database with the corresponding id.
     *
     * @param array $file
     */
    public function update(array $file): string
    {
        if (!Request::isPost()) {
            $status = 'error';
            $error  = 'This route can only be accessed with POST headers';
            return json_encode(compact('status', 'error'));
        }

        $params  = Request::getParams();
        $task    = $params->get('task');
        $file    = $params->get('file');
        $status  = 'success';

        // Actual processing of the file.
        $file = new File($file['id']);

        if (!$this->hasRights($file)) {
            return error('403');
        }

        if ($file->getName() !== $file['name']) {
            $nameSet = $file->setName($file['name']);
        }

        if ($file->getContent() !== $file['content']) {
            $contentSet = $file->setContent($file['content']);
        }


        if ($nameSet && $contentSet) {
            $data = $file->getAll();
        } else {
            $status = 'error';
            $error  = 'File ' . $file['name'] . ' could not be uploaded.';
            return json_encode(compact('status', 'error'));
        }

        return json_encode(compact('status', 'data'));
    }

    public function hasRights($file): bool
    {
        $task     = Task::get($file->getTaskId());
        $creator  = $task->getCreator()->getId();
        $assignee = $task->getAssignee()->getId();

        return ($_SESSION['id'] == $creator|| $_SESSION['id'] == $assignee);
    }

    /**
     * Delete file by id.
     *
     * Delete file by the id given in the POST headers.
     *
     * @return string
     */
    public function delete(): string
    {
        $fileId = Request::getParams()->get('id');
        $file   = new File($fileId);

        if (!$this->hasRights($file)) {
            return error('403');
        }

        if ($file->delete()) {
            // Just some hardcoded string.
            return '{"status": "success"}';
        } else {
            $status = 'error';
            $error  = 'Something went wrong deleting the file '
                      . $fileToDelete['name'] .  '.';
            // Not just some hardcoded string.
            return json_encode(compact('status', 'error'));
        }
    }
}
