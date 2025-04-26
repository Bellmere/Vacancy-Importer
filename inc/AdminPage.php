<?php

namespace VacanceImporter;

class AdminPage {
    public static function register() {
        add_menu_page(
            'Vacancy Importer',
            'Vacancy Importer',
            'manage_options',
            'vacancy-importer',
            [self::class, 'render_page'],
            'dashicons-upload',
            26
        );
    }

    public static function render_page() {
        if (isset($_POST['vacancy_import']) && check_admin_referer('vacancy_import_action')) {
            $importer = new CSVImporter();
            $importer->import(plugin_dir_path(__DIR__, 2) . 'vacancies_pl_60.csv');
            echo '<div class="updated"><p>Импорт завершён успешно!</p></div>';
        }

        ?>
        <div class="wrap">
            <h1>Импортировать Вакансии</h1>
            <form method="post">
                <?php wp_nonce_field('vacancy_import_action'); ?>
                <input type="submit" name="vacancy_import" class="button button-primary" value="Импортировать вакансии">
            </form>
        </div>
        <?php
    }
}
