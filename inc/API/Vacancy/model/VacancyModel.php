<?php

namespace VacanceImporter\API\Vacancy\model;

class VacancyModel {
    private $wpdb;

    public function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
    }

    public function get_vacancies(
        $city = '',
        $per_page = 10,
        $page = 1,
        $salary_min = null,
        $salary_max = null,
        $order_by = 'p.ID',
        $order = 'DESC'
    ) {
        $offset = ($page - 1) * $per_page;
        $sql_params = [];

        $allowed_order_by = [
            'id' => 'p.ID',
            'salary' => 'CAST(salary_meta.meta_value AS UNSIGNED)',
        ];

        $order_by_sql = $allowed_order_by[$order_by] ?? 'p.ID';
        $order = strtoupper($order) === 'ASC' ? 'ASC' : 'DESC';

        $sql = "
        SELECT SQL_CALC_FOUND_ROWS 
            p.ID, 
            p.post_title, 
            p.post_content,
            city_meta.meta_value AS city,
            salary_meta.meta_value AS salary,
            type_meta.meta_value AS type_of_employment
        FROM wp_posts p
        LEFT JOIN wp_postmeta AS city_meta 
            ON p.ID = city_meta.post_id 
            AND city_meta.meta_key = 'city'
        LEFT JOIN wp_postmeta AS salary_meta 
            ON salary_meta.post_id = p.ID 
            AND salary_meta.meta_key = 'salary'
        LEFT JOIN wp_postmeta AS type_meta 
            ON type_meta.post_id = p.ID 
            AND type_meta.meta_key = 'type_of_employment'
        WHERE p.post_type = 'vacancy'
          AND p.post_status = 'publish'
    ";

        if (!empty($city)) {
            $sql .= " AND city_meta.meta_value LIKE %s ";
            $sql_params[] = '%' . $this->wpdb->esc_like($city) . '%';
        }

        if (!is_null($salary_min)) {
            $sql .= " AND CAST(salary_meta.meta_value AS UNSIGNED) >= %d ";
            $sql_params[] = $salary_min;
        }

        if (!is_null($salary_max)) {
            $sql .= " AND CAST(salary_meta.meta_value AS UNSIGNED) <= %d ";
            $sql_params[] = $salary_max;
        }

        $sql .= " ORDER BY {$order_by_sql} {$order} LIMIT %d OFFSET %d";
        $sql_params[] = $per_page;
        $sql_params[] = $offset;

        $prepared_query = $this->wpdb->prepare($sql, ...$sql_params);
        $results = $this->wpdb->get_results($prepared_query);
        $total = $this->wpdb->get_var("SELECT FOUND_ROWS()");

        $normalized_posts = array_map(function($post) {
            return [
                'id' => (int) $post->ID,
                'title' => $post->post_title,
                'content' => $post->post_content,
                'city' => $post->city,
                'salary' => $post->salary,
                'type_of_employment' => $post->type_of_employment,
            ];
        }, $results);

        return [
            'posts' => $normalized_posts,
            'total' => (int) $total,
        ];
    }

    public function add_or_update_vacancy($title, $description, $city, $salary, $type_of_employment) {
        $existing_post_id = $this->wpdb->get_var(
            $this->wpdb->prepare(
                "SELECT ID FROM {$this->wpdb->posts} WHERE post_title = %s AND post_type = 'vacancy' LIMIT 1",
                $title
            )
        );

        if ($existing_post_id) {
            wp_update_post([
                'ID' => (int) $existing_post_id,
                'post_content' => $description,
            ]);
            $post_id = (int) $existing_post_id;
        } else {
            $post_id = wp_insert_post([
                'post_type' => 'vacancy',
                'post_title' => $title,
                'post_content' => $description,
                'post_status' => 'publish',
            ]);
        }

        if (!is_wp_error($post_id)) {
            update_field('city', $city, $post_id);
            update_field('salary', (int)$salary, $post_id);
            update_field('type_of_employment', $type_of_employment, $post_id);
        }

        return $post_id;
    }
}
