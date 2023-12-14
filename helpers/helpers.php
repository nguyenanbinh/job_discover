<?php

/**
 * @param string $path
 * @return string
 */
function basePath(string $path = ""): string
{
    return dirname(__DIR__) . "/$path";
}