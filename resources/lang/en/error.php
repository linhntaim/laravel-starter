<?php

return [
    'def' => [
        'abort' => [
            '403' => 'Not authorized',
            '404' => 'Not found',
        ],
    ],

    'exceptions' => [
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
        'unhandled_exception' => [
            'level_failed' => 'Something went wrong',
            'level' => ':message',
        ],
        'user_exception' => [
            'level_failed' => 'Something went wrong with user action',
            'level' => ':message',
        ],
        'one_time_event_registered_exception' => [
            'level_failed' => 'Something went wrong with user action',
            'level' => ':message',
        ],
        'event_watcher_registered_exception' => [
            'level_failed' => 'Something went wrong with user action',
            'level' => ':message',
        ],
    ],

    'http' => [
        'controllers' => [
            'api' => [
                'account' => [
                ],
                'admin' => [
                ],
                'home' => [
                ],
            ],
        ],
        'middleware' => [
            'authenticate' => [
                'unauthenticated' => 'The user credentials were incorrect',
            ],
        ],
    ],

    'model_repositories' => [
    ],

    'imports' => [
    ],

    'rules' => [
        'current_password' => 'The current password must be matched',
        'not_trashed' => 'The :attribute has already been trashed',
        'trashed' => 'The :attribute has not already been trashed',
    ],

    'utils' => [
        'files' => [
            'filer' => [
                'filer' => [
                    'file_not_found' => 'File is not found',
                ],
            ],
            'file_reader' => [
                'csv_reader' => [
                    'read_line' => 'Error at line :line',
                ],
            ],
            'file_writer' => [
                'zip_archive_handler' => [
                    'cannot_opened' => 'Cannot open zip file',
                ],
                'zip_handler' => [
                    'opened' => 'Zip file was opened',
                    'not_opened' => 'Zip file was not opened',
                    'not_found' => 'File for zipping was not found',
                ],
            ],
            'file_helper' => [
                'directory_not_found' => 'Directory is not found',
                'directory_not_allowed' => 'Directory is not allowed to access',
                'directory_not_writable' => 'Directory is not writable',
            ],
        ],
        'handled_files' => [
            'filer' => [
                'csv_filer' => [
                    'read_line' => 'Error at line :line',
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
