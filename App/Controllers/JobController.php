<?php

namespace App\Controllers;

use Framework\Authorization;
use Framework\Database;
use Framework\Session;
use Framework\Validation;

class JobController
{
    protected $db;

    public function __construct()
    {
        $config = require basePath("config/db.php");
        $this->db = new Database($config);
    }

    public function index()
    {
        $jobs = $this->db->query("SELECT * FROM LISTINGS  ORDER BY CREATED_AT")->fetchAll();

        loadView("jobs/index", [
            "jobs" => $jobs
        ]);
    }

    public function show($params)
    {
        $id = $params["id"] ?? "";

        $params = [
            "id" => $id
        ];

        $job = $this->db->query("SELECT * FROM LISTINGS WHERE id = :id", $params)->fetch();

        if (!$job) {
            ErrorController::notFound("Job not found");
            return;
        }
        loadView("jobs/show", ["job" => $job]);
    }

    public function create()
    {
        loadView("jobs/create");
    }

    public function store()
    {
        $allowedFields = ["title", "description", "salary", "tags", "company", "address", "city", "state", "phone", "email", "requirements", "benefits"];

        $newListingData = array_intersect_key($_POST, array_flip($allowedFields));

        $newListingData = array_map("sanitize", $newListingData);

        $newListingData["user_id"] = Session::get("user")["id"];

        $requiredFields = ["title", "description", "salary", "email", "city", "state"];

        $errors = [];

        foreach ($requiredFields as $field) {
            $checkEmpty = empty($newListingData[$field]);
            $checkLength = !Validation::string($newListingData[$field]);

            if ($checkEmpty || $checkLength) {
                $errors[$field] = ucfirst($field) . " is required";
            }
        }

        if (!empty($errors)) {
            loadView("jobs/create", [
                "errors" => $errors,
                "job" => $newListingData
            ]);
        } else {
            $fields = [];

            foreach ($newListingData as $field => $value) {
                $fields[] = $field;
            }

            $fields = implode(", ", $fields);

            $values = [];

            foreach ($newListingData as $field => $value) {
                // Convert empty strings to null
                if ($value === "") {
                    $newListingData[$field] = null;
                }
                $values[] = ":" . $field;
            }

            $values = implode(", ", $values);

            $query = "INSERT INTO LISTINGS ({$fields}) VALUES ({$values})";

            $this->db->query($query, $newListingData);

            redirect("/jobs");
        }
    }

    /**
     * Show the listing edit form
     * 
     * @param array $params
     * @return void
     */
    public function edit($params)
    {
        $id = $params['id'] ?? '';

        $params = [
            'id' => $id
        ];

        $job = $this->db->query('SELECT * FROM listings WHERE id = :id', $params)->fetch();

        // Check if listing exists
        if (!$job) {
            ErrorController::notFound('Listing not found');
            return;
        }
        // Authorization
        if (!Authorization::isOwner($job->user_id)) {
            Session::setFlashMessage('error_message', 'You are not authorized to update this job');
            return redirect('/jobs/' . $job->id);
        }

        loadView('jobs/edit', [
            'job' => $job
        ]);
    }

    /**
     * Update a listing
     * 
     * @param array $params
     * @return void
     */
    public function update($params)
    {
        $id = $params['id'] ?? '';

        $params = [
            'id' => $id
        ];

        $job = $this->db->query('SELECT * FROM listings WHERE id = :id', $params)->fetch();

        // Check if listing exists
        if (!$job) {
            ErrorController::notFound('Listing not found');
            return;
        }


        $allowedFields = ['title', 'description', 'salary', 'tags', 'company', 'address', 'city', 'state', 'phone', 'email', 'requirements', 'benefits'];

        $updateValues = [];

        $updateValues = array_intersect_key($_POST, array_flip($allowedFields));

        $updateValues = array_map('sanitize', $updateValues);

        $requiredFields = ['title', 'description', 'salary', 'email', 'city', 'state'];

        $errors = [];

        foreach ($requiredFields as $field) {
            if (empty($updateValues[$field]) || !Validation::string($updateValues[$field])) {
                $errors[$field] = ucfirst($field) . ' is required';
            }
        }

        if (!empty($errors)) {
            loadView('listings/edit', [
                'job' => $job,
                'errors' => $errors
            ]);
            exit;
        } else {
            // Submit to database
            $updateFields = [];

            foreach (array_keys($updateValues) as $field) {
                $updateFields[] = "{$field} = :{$field}";
            }

            $updateFields = implode(', ', $updateFields);

            $updateQuery = "UPDATE listings SET $updateFields WHERE id = :id";

            $updateValues['id'] = $id;
            $this->db->query($updateQuery, $updateValues);

            // Set flash message
            $_SESSION["success_message"] = "Job updated successfully";

            redirect('/jobs/' . $id);
        }
    }

    /**
     * Delete a job
     * 
     * @param array $params
     * @return void
     */
    public function destroy($params)
    {
        $id = $params["id"];

        $params = [
            "id" => $id
        ];

        $job = $this->db->query("SELECT * FROM LISTINGS WHERE id = :id", $params)->fetch();

        // Check if job exists
        if (!$job) {
            ErrorController::notFound("Job not found");
            return;
        }


        // Authorization
        if (!Authorization::isOwner($job->user_id)) {
            Session::setFlashMessage('error_message', 'You are not authorized to delete this job');
            return redirect('/jobs/' . $job->id);
        }
        $this->db->query("DELETE FROM LISTINGS WHERE id = :id", $params);

        // Set flash message
        Session::setFlashMessage("success_message", "Job deleted successfully");

        redirect("/jobs");
    }

    /**
     * Search listings by keywords/location
     * 
     * @return void
     */
    public function search()
    {
        $keywords = isset($_GET['keywords']) ? trim($_GET['keywords']) : '';
        $location = isset($_GET['location']) ? trim($_GET['location']) : '';

        $query = "SELECT * FROM listings WHERE (title LIKE :keywords OR description LIKE :keywords OR tags LIKE :keywords OR company LIKE :keywords) AND (city LIKE :location OR state LIKE :location)";

        $params = [
            'keywords' => "%{$keywords}%",
            'location' => "%{$location}%"
        ];

        $jobs = $this->db->query($query, $params)->fetchAll();

        loadView('/jobs/index', [
            'jobs' => $jobs,
            'keywords' => $keywords,
            'location' => $location
        ]);
    }
}
