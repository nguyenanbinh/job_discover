<?php

use JetBrains\PhpStorm\NoReturn;

/**
 * Get the base path
 *
 * @param string $path
 * @return string
 */
function basePath(string $path = ""): string
{
    return dirname(__DIR__) . "/$path";
}

/**
 * Load a view
 *
 * @param string $name
 * @return void
 */
function loadView(string $name = "", array $data = []): void
{
    $viewPath = basePath("views/$name.view.php");

    if (file_exists($viewPath)) {
        extract($data);
        require $viewPath;
    } else {
        echo "View $name not found";
    }
}

/**
 * Load a partial
 *
 * @param string $name
 * @return void
 */
function loadPartial(string $name = "", array $data = []): void
{
    $partialPath = basePath("views/partials/$name.view.php");

    if (file_exists($partialPath)) {
        extract($data);
        require $partialPath;
    } else {
        echo "Partial view $name not found";
    }
}

/**
 * Inspect a value(s)
 *
 * @param mixed $value
 * @return void
 */
function inspect(mixed $value): void
{
    echo '<pre>';
    var_dump($value);
    echo '</pre>';
}

/**
 * Inspect a value(s) and die
 *
 * @param mixed $value
 * @return void
 */
function inspectAndDie(mixed ...$value): void
{
    echo '<pre>';
    die(var_dump($value));
    echo '</pre>';
}

/**
 * Format salary
 *
 * @param string $salary
 * @return string Formatted Salary
 */
function formatSalary($salary)
{
    return '$' . number_format(floatval($salary));
}

/**
 * Sanitize form input
 *
 * @param string $dirtyStr
 * @return string 
 */
function sanitize($dirtyStr)
{
    return filter_var($dirtyStr, FILTER_SANITIZE_SPECIAL_CHARS);
}

/**
 * Retrieve old input
 *
 * @param string $string
 * @param string $init
 * @return string 
 */
function old($string, $init = "")
{
    return $string ?? $init;
}

/**
 * Redirect to a given url
 * 
 * @param string $url
 * @return void
 */
function redirect($url)
{
  header("Location: {$url}");
  exit;
}