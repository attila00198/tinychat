<?php

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

class View
{
    private static $viewsPath = __DIR__ . '/../views/';
    private static $data = [];
    private static $layout = null;

    /**
     * Set the views directory path
     */
    public static function setViewsPath($path)
    {
        self::$viewsPath = rtrim($path, '/') . '/';
        echo "<!-- Views path set to: " . self::$viewsPath . " -->\n";
    }

    /**
     * Set the default layout
     */
    public static function setLayout($layout)
    {
        self::$layout = $layout;
        echo "<!-- Default layout set to: " . $layout . " -->\n";
    }

    /**
     * Render a view with optional data and layout
     */
    public static function render($template, $data = [], $layout = null)
    {
        try {
            echo "<!-- Rendering template: $template -->\n";

            // Merge with any previously set data
            $viewData = array_merge(self::$data, $data);
            echo "<!-- View data: " . print_r($viewData, true) . " -->\n";

            // Clear the data after use
            self::$data = [];

            // Build the full path to the template
            $templatePath = self::$viewsPath . $template . '.php';
            echo "<!-- Template path: $templatePath -->\n";

            // Check if template exists
            if (!file_exists($templatePath)) {
                throw new Exception("View template not found: {$templatePath}");
            }
            echo "<!-- Template exists -->\n";

            // Extract data variables for use in template
            extract($viewData);

            // Start output buffering for the main content
            ob_start();

            // Include the template
            include $templatePath;

            // Get the main content
            $content = ob_get_clean();
            echo "<!-- Content length: " . strlen($content) . " -->\n";

            // If layout is specified (or default layout exists), wrap content in layout
            $layoutToUse = $layout ?? self::$layout;
            if ($layoutToUse) {
                echo "<!-- Using layout: $layoutToUse -->\n";
                $layoutPath = self::$viewsPath . $layoutToUse . '.php';
                echo "<!-- Layout path: $layoutPath -->\n";

                if (!file_exists($layoutPath)) {
                    throw new Exception("Layout template not found: {$layoutPath}");
                }
                echo "<!-- Layout exists -->\n";

                // Start output buffering for layout
                ob_start();

                // Include the layout (content variable will be available)
                include $layoutPath;

                // Get the final output
                $output = ob_get_clean();
            } else {
                echo "<!-- No layout used -->\n";
                $output = $content;
            }

            echo "<!-- Final output length: " . strlen($output) . " -->\n";
            return $output;
        } catch (Exception $e) {
            echo "<h1>View Error</h1>";
            echo "<p>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
            echo "<p>File: " . htmlspecialchars($e->getFile()) . "</p>";
            echo "<p>Line: " . $e->getLine() . "</p>";
            echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
            return "";
        }
    }

    /**
     * Set data that will be available to all views
     */
    public static function share($key, $value = null)
    {
        if (is_array($key)) {
            self::$data = array_merge(self::$data, $key);
        } else {
            self::$data[$key] = $value;
        }
    }

    /**
     * Set data for the next view only
     */
    public static function with($key, $value = null)
    {
        if (is_array($key)) {
            self::$data = array_merge(self::$data, $key);
        } else {
            self::$data[$key] = $value;
        }

        return new self();
    }

    /**
     * Debug method to check paths and files
     */
    public static function debug()
    {
        echo "<h2>View Debug Information</h2>";
        echo "<p><strong>Views Path:</strong> " . self::$viewsPath . "</p>";
        echo "<p><strong>Path exists:</strong> " . (is_dir(self::$viewsPath) ? 'YES' : 'NO') . "</p>";
        echo "<p><strong>Default Layout:</strong> " . (self::$layout ?? 'None') . "</p>";

        if (is_dir(self::$viewsPath)) {
            echo "<p><strong>Files in views directory:</strong></p>";
            echo "<ul>";
            $files = scandir(self::$viewsPath);
            foreach ($files as $file) {
                if ($file !== '.' && $file !== '..') {
                    echo "<li>$file</li>";
                }
            }
            echo "</ul>";
        }
    }
}

/**
 * Global helper function for rendering views
 */
function view($template, $data = [], $layout = null)
{
    return View::render($template, $data, $layout);
}
