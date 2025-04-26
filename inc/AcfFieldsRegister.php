<?php

namespace VacanceImporter;

class AcfFieldsRegister {
    public static function register() {
        if (!function_exists('acf_add_local_field_group')) {
            return;
        }

        acf_add_local_field_group([
            'key' => 'group_vacancy_fields',
            'title' => 'Vacancy Fields',
            'fields' => [
                [
                    'key' => 'field_vacancy_city',
                    'label' => 'City',
                    'name' => 'city',
                    'type' => 'text',
                    'required' => 0,
                ],
                [
                    'key' => 'field_vacancy_salary',
                    'label' => 'Salary',
                    'name' => 'salary',
                    'type' => 'number',
                    'required' => 0,
                ],
                [
                    'key' => 'field_vacancy_type_of_employment',
                    'label' => 'Type of Employment',
                    'name' => 'type_of_employment',
                    'type' => 'text',
                    'required' => 0,
                ],
            ],
            'location' => [
                [
                    [
                        'param' => 'post_type',
                        'operator' => '==',
                        'value' => 'vacancy',
                    ],
                ],
            ],
        ]);
    }
}
