<?php

namespace VacanceImporter;

class PostTypeRegister {
    public static function register() {
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
    }
}
