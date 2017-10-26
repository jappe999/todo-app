<?php

use Core\Router as Router;

// --- Regular routes --- //
Router::get('/', 'HomeController@get');
Router::get('/test', 'HomeController@test');

// --- Authentication routes --- //

// Register
Router::get('/register', 'AuthenticateController@getRegister');
Router::post('/register', 'AuthenticateController@register');

// Login
Router::get('/login', 'AuthenticateController@getLogin');
Router::post('/login', 'AuthenticateController@login');

// Logout
Router::get('/logout', 'AuthenticateController@logout');


// --- API endpoints --- //

// Users endpoints
Router::get('/api/users/get/{user_id}', 'UsersController@getUser');
Router::get('/api/users/get', 'UsersController@getAll');
Router::post('/api/users/add', 'UsersController@addNew');
Router::post('/api/users/update', 'UsersController@update');
Router::post('/api/users/delete', 'UsersController@delete');

// Tasks endpoints
Router::get('/api/tasks/get', 'TasksController@getAll');
Router::get('/api/tasks/todo/get', 'TasksController@getAllTodos');
Router::get('/api/tasks/done/get', 'TasksController@getAllDone');
Router::get('/api/tasks/get/{task_id}', 'TasksController@getTask');
Router::post('/api/tasks/add', 'TasksController@addNew');
Router::post('/api/tasks/update', 'TasksController@update');
Router::post('/api/tasks/delete', 'TasksController@delete');

// Files endpoints
Router::get('/api/files/get/{file_id}', 'FilesController@getFile');
Router::post('/api/files/add', 'FilesController@addNew');
Router::post('/api/files/update', 'FilesController@update');
Router::post('/api/files/delete', 'FilesController@delete');
