<?php

$router->get("/", "HomeController@index");

// Jobs list
$router->get("/jobs", "JobController@index");
$router->get("/jobs/create", "JobController@create", ['auth']);
$router->get("/jobs/edit/{id}", "JobController@edit", ['auth']);
$router->get('/jobs/search', 'JobController@search');
$router->get("/jobs/{id}", "JobController@show");

$router->post("/jobs", "JobController@store", ['auth']);
$router->put("/jobs/{id}", "JobController@update", ['auth']);
$router->delete('/jobs/{id}', 'JobController@destroy', ['auth']);

// Auth
$router->get('/auth/register', 'UserController@create', ['guest']);
$router->get('/auth/login', 'UserController@login', ['guest']);

$router->post('/auth/register', 'UserController@store', ['guest']);
$router->post('/auth/logout', 'UserController@logout', ['auth']);
$router->post('/auth/login', 'UserController@authenticate', ['guest']);