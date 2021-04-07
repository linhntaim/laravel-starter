<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

return [
    'def' => [
        'abort' => [
            '403' => 'Not authorized',
            '404' => 'Not found',
            // TODO:

            // TODO
        ],
        // TODO:

        // TODO
    ],

    'exceptions' => [
        'handler' => [
            'method_not_allowed' => 'The :method method is not supported for this route. Supported methods: :allow.',
            'throttle_requests' => 'Too Many Attempts.',
        ],
        'app_exception' => [
            'level_failed' => 'Something went wrong with application',
            'level' => ':message',
        ],
        'database_exception' => [
            'level_failed' => 'Something went wrong with database',
            'level' => ':message',
        ],
        'default_exception' => [
            'level_failed' => 'Something went wrong with application',
        ],
        'exception' => [
            'level_failed' => 'Something went wrong',
            'level' => ':message',
        ],
        'ip_limit_exception' => [
            'level_failed' => 'Your IP is not allowed',
            'level' => ':message',
        ],
        'unhandled_exception' => [
            'level_failed' => 'Something went wrong',
            'level' => ':message',
        ],
        'user_exception' => [
            'level_failed' => 'Something went wrong with user action',
            'level' => ':message',
        ],
        // TODO:

        // TODO
    ],

    'http' => [
        'controllers' => [
            'api' => [
                'account' => [
                    'admin_account_controller' => [
                        'current_password' => [
                            'required' => 'The current password field is required',
                            'current_password_as_password' => 'The password is incorrect',
                        ],
                        'display_name' => [
                            'max' => 'The display name may not be greater than 255 characters',
                            'required' => 'The display name field is required',
                        ],
                        'email' => [
                            'email' => 'The email must be a valid email address',
                            'max' => 'The email may not be greater than 255 characters',
                            'not_trashed' => 'The email has already been trashed',
                            'required' => 'The email field is required',
                            'unique_not_trashed' => 'The email has already been taken or trashed',
                            'unique' => 'The email has already been taken',
                        ],
                        'image' => [
                            'dimensions' => 'The image has invalid dimensions (the minimum is 512px * 512px)',
                            'image' => 'The image must be an image',
                            'required' => 'The image field is required',
                        ],
                        'password' => [
                            'confirmed' => 'The password confirmation does not match',
                            'confirmed_new' => 'The password confirmation does not match',
                            'min' => 'The password must be at least 8 characters',
                            'required' => 'The display name field is required',
                        ],
                        // TODO:

                        // TODO
                    ],
                    // TODO:

                    // TODO
                ],
                'admin' => [
                    // TODO:

                    // TODO
                ],
                'home' => [
                    // TODO:

                    // TODO
                ],
            ],
        ],
        'middleware' => [
            'authenticate' => [
                'unauthenticated' => 'The user credentials were incorrect',
            ],
            'authorized_with_admin' => [
                'must_be_admin' => 'The user must be an admin',
            ],
            // TODO:

            // TODO
        ],
    ],

    'model_repositories' => [
        // TODO:

        // TODO
    ],

    'imports' => [
        // TODO:

        // TODO
    ],

    'rules' => [
        'current_password' => 'The current password must be matched',
        'not_trashed' => 'The :attribute has already been trashed',
        'trashed' => 'The :attribute has not already been trashed',
        // TODO:

        // TODO
    ],

    'utils' => [
        'handled_files' => [
            'filer' => [
                'csv_filer' => [
                    'read_line' => 'Error at line :line',
                    'read' => ':message (Line :line)',
                ],
                'filer' => [
                    'read_count' => 'Error at count :count',
                    'read' => ':message (Count :count)',
                ],
            ],
        ],
        'regex' => [
            'regex_parser' => [
                'unexpected_token' => 'The regular expression pattern is not valid',
            ],
            'regex_based_string_generator' => [
                'generator' => [
                    'unexpected_token' => 'The regular expression token is not supported',
                ],
            ],
        ],
    ],
];
