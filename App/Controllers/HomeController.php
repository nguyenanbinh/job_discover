<?php

namespace App\Controllers;

use Framework\Database;

class HomeController
{
  protected $db;

  public function __construct()
  {
    $config = require basePath('config/db.php');
    $this->db = new Database($config);
  }

  /*
   * Show the latest listings
   * 
   * @return void
   */
  public function index()
  {
    $jobs = $this->db->query('SELECT * FROM LISTINGS ORDER BY CREATED_AT LIMIT 6')->fetchAll();

    loadView('home', [
      'jobs' => $jobs
    ]);
  }
}