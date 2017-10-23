<?php

namespace Controllers;

use Models\User as User;
use Models\File as File;
use Core\Database as DB;
use Core\Request as Request;

/**
 * HomeController
 */
class FilesController extends Controller
{

    /**
     * Adds new files
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

    public function getFile($fileId)
    {
        $query = "SELECT content FROM files
                  WHERE id=:id";
        $stmt  = DB::prepare($query);

        $stmt->bindParam(':id', $fileId);
        $stmt->execute();

        $content = $stmt->fetch()['content'];
        $content = substr($content, 5);

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
}
