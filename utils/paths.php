<?php

/**
 * Paths Class
 *
 * This class provides utility methods for handling file paths within a web application.
 * It helps include files relative to the document root and retrieve absolute paths
 * from the document root for better code organization and maintainability.
 *
 * @example
 * ```php
 * $paths = new Paths();
 * $paths->include('/path/to/file.php'); // Includes a file relative to the document root.
 *
 * $absolutePath = Paths::from_root('/path/to/file.php');
 * include_once($absolutePath); // Includes a file using an absolute path.
 * ```
 */
class Paths {
    private $root;

    /**
     * Paths constructor.
     *
     * Initializes the Paths class by setting the document root path.
     */
    public function __construct() {
        $this->root = $_SERVER['DOCUMENT_ROOT'];
    }

    /**
     * Include a file relative to the document root.
     *
     * @param string $path The relative path to the file to be included.
     * @return void
     */
    public function include($path) {
        include($this->root . $path);
    }

    /**
     * Include a file once relative to the document root.
     *
     * @param string $path The relative path to the file to be included once.
     * @return void
     */
    public function include_once($path) {
        include($this->root . $path);
    }

    /**
     * Get an absolute path from the document root.
     *
     * @param string $path The relative path to convert to an absolute path.
     * @return string The absolute path derived from the document root.
     */
    public static function from_root($path) {
        return $_SERVER['DOCUMENT_ROOT'] . $path;
    }
}

?>