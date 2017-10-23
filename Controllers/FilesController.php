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
            $file['id'] = $fileId;
            $data[]     = $file;
        } else {
            $status = 'error';
            $error  = 'File ' . $file['name'] . ' could not be uploaded.';
            return json_encode(compact('status', 'error'));
        }

        return json_encode(compact('status', 'data'));
    }
}
