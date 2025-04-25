<?php
/**
 * Plugin Name: Vacancy Importer
 * Description: Custom plugin to import job vacancies.
 * Version: 1.0
 * Author: Saur0n
 */

use VacanceImporter\CSVImporter;

if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
    require_once __DIR__ . '/vendor/autoload.php';
}

add_action('admin_init', function() {
    if (isset($_GET['import_vacancies']) && current_user_can('manage_options')) {
        $importer = new CSVImporter();
        $importer->import(plugin_dir_path(__FILE__) . 'vacancies_pl_60.csv');
    }
});

add_action('init', function() {
    register_post_type('vacancy', [
        'labels' => [
            'name' => 'Vacancies',
            'singular_name' => 'Vacancy',
        ],
        'public' => true,
        'has_archive' => true,
        'show_in_rest' => true,
        'supports' => ['title', 'editor'],
    ]);
});
