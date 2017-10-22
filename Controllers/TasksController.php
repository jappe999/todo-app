<?php

namespace Controllers;

use Models\Task as Task;
use Core\Request as Request;


class TasksController extends Controller
{

    public function getAll(): string
    {
        $data   = Task::getAll();
        $status = $data !== false ? 'success' : 'error';
        return json_encode(compact('status', 'data'));
    }

    public function getAllTodos(): string
    {
        $data   = Task::getAllTodos();
        $status = $data !== false ? 'success' : 'error';
        return json_encode(compact('status', 'data'));
    }

    public function getAllDone(): string
    {
        $data   = Task::getAllDone();
        $status = $data !== false ? 'success' : 'error';
        return json_encode(compact('status', 'data'));
    }

    public function getTask($task_id): string
    {
        $data   = Task::get($task_id);
        $status = $data !== false ? 'success' : 'error';
        return json_encode(compact('status', 'data'));
    }

    // TODO: Rewrite to new model
    public function addNew()
    {
        if (!Request::isPost())
            return error('405');

        $params = Request::getParams();
        $title  = $params->get('title');

        if ($task = Task::add($title)) {
            if ($params->has('description'))
                $task->setDescription($params->get('description'));

            if ($params->has('assignee'))
                $task->setAssignee($params->get('assignee'));

            $status = 'success';
            $data   = $task->get();

            return json_encode(compact('status', 'data'));
        }
    }

    public function update()
    {
        $params = Request::getParams();
        $id     = $params->get('id');
        $task   = Task::get($id);

        if (!Request::isPost())
            return error('405');

        if ($params->has('title'))
            $task->setTitle($params->get('title'));

        if ($params->has('description'))
            $task->setDescription($params->get('description'));

        if ($params->has('assignee'))
            $task->setAssignee($params->get('assignee'));

        if ($params->has('is_done'))
            $task->setDone($params->get('is_done'));

        $status = 'success';
        return json_encode(compact('status'));
    }

    public function delete()
    {
        if (!Request::isPost())
            return error('405');

        $taskId = Request::getParams()->get('id');
        if (Task::delete($taskId)) {
            $status = 'success';
        } else {
            $status = 'error';
        }

        return json_encode(compact('status'));
    }
}
