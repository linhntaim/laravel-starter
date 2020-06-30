<?php

return [
    'def' => [
        'abort' => [
            '403' => '認証されていません',
            '404' => 'ページが見つかりません。',
        ],
    ],

    'exceptions' => [
        'app_exception' => [
            'level_failed' => 'システムエラーが発生しました。しばらくしてからもう一度お試しください。',
            'level' => ':message',
        ],
        'database_exception' => [
            'level_failed' => 'データベースに問題が発生しました',
            'level' => ':message',
        ],
        'default_exception' => [
            'level_failed' => 'システムエラーが発生しました。しばらくしてからもう一度お試しください。',
        ],
        'exception' => [
            'level_failed' => '問題が発生しました',
            'level' => ':message',
        ],
        'unhandled_exception' => [
            'level_failed' => '問題が発生しました',
            'level' => ':message',
        ],
        'user_exception' => [
            'level_failed' => 'ユーザーアクションに問題が発生しました',
            'level' => ':message',
        ],
        'one_time_event_registered_exception' => [
            'level_failed' => 'ユーザーアクションに問題が発生しました',
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
                'unauthenticated' => 'メールアドレス、もしくはパスワードが異なります。'
            ]
        ],
    ],

    'model_repositories' => [
    ],

    'imports' => [
    ],

    'rules' => [
        'current_password' => '入力されたパスワードが間違っています。',
        'not_trashed' => 'The :attribute has already been trashed',
        'trashed' => 'The :attribute has not already been trashed',
    ],

    'utils' => [
        'files' => [
            'filer' => [
                'filer' => [
                    'file_not_found' => 'ファイルが見つかりません',
                ],
            ],
            'file_reader' => [
                'csv_reader' => [
                    'read_line' => ':line行目に入力データに誤りがあります。',
                ],
            ],
            'file_writer' => [
                'zip_archive_handler' => [
                    'cannot_opened' => 'zipファイルを開けません',
                ],
                'zip_handler' => [
                    'opened' => 'Zipファイルが開かれました',
                    'not_opened' => 'Zipファイルが開かれていません',
                    'not_found' => 'zipファイルは見つかりませんでした',
                ],
            ],
            'file_helper' => [
                'directory_not_found' => 'ディレクトリが見つかりません',
                'directory_not_allowed' => 'ディレクトリへのアクセスは許可されていません',
                'directory_not_writable' => 'ディレクトリは書き込み不可',
            ],
        ],
        'regex' => [
            'regex_parser' => [
                'unexpected_token' => '正規表現パターンが無効です',
            ],
            'regex_based_string_generator' => [
                'generator' => [
                    'unexpected_token' => 'The regular expression token is not supported',
                ],
            ],
        ],
    ],
];
