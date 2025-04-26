<?php

namespace VacanceImporter;

use League\Csv\Reader;

class CSVImporter {
    public function import($csvPath) {
        $csv = Reader::createFromPath($csvPath, 'r');
        $csv->setHeaderOffset(0);

        foreach ($csv->getRecords() as $record) {
            $title = $record['Название'] ?? '';
            $city = $record['Город'] ?? '';
            $salary = $record['Зарплата (PLN)'] ?? '';
            $type_of_employment = $record['Тип занятости'] ?? '';
            $description = $record['Описание'] ?? '';

            if (empty($title)) {
                continue;
            }

            $salary_clean = (int) preg_replace('/[^0-9]/', '', $salary);

            //  =====   Cheking Dublicates  =====
            $existing = get_posts([
                'post_type'  => 'vacancy',
                'title'      => $title,
                'meta_query' => [
                    'relation' => 'AND',
                    [
                        'key'     => 'city',
                        'value'   => $city,
                        'compare' => '=',
                    ],
                    [
                        'key'     => 'salary',
                        'value'   => $salary,
                        'compare' => '=',
                    ],
                ],
                'posts_per_page' => 1,
            ]);

            if (!empty($existing)) {
                continue;
            }

            //  =====   Create New Vacancy  =====
            $post_id = wp_insert_post([
                'post_type'    => 'vacancy',
                'post_title'   => sanitize_text_field($title),
                'post_content' => sanitize_textarea_field($description),
                'post_status'  => 'publish',
            ]);

            if (!is_wp_error($post_id)) {
                //  =====   Save ACF Fields =====
                update_field('city', sanitize_text_field($city), $post_id);
                update_field('salary', $salary_clean, $post_id);
                update_field('type_of_employment', sanitize_text_field($type_of_employment), $post_id);
            }
        }
    }
}
