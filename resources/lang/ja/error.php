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
                    'admin_account_controller' => [
                        'current_password' => [
                            'required' => '現在のパスワードフィールドが必要です',
                            'current_password_as_password' => 'パスワードは正しくありません。',
                        ],
                        'display_name' => [
                            'max' => 'The display name may not be greater than 255 characters',
                            'required' => '表示名フィールドは必須です',
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
                            'dimensions' => '画像のサイズが無効です（最小値は512px * 512pxです)',
                            'image' => '画像は画像でなければなりません',
                            'required' => '画像フィールドは必須です',
                        ],
                        'password' => [
                            'confirmed' => 'パスワードが一致しません',
                            'confirmed_new' => '入力した新しいパスワードと確認用のパスワードが一致しません。',
                            'min' => 'パスワードは少なくとも8文字でなければなりません',
                            'required' => '表示名フィールドは必須です',
                        ],
                    ],
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
        'handled_files' => [
            'filer' => [
                'csv_filer' => [
                    'read_line' => ':line行目に入力データに誤りがあります。',
                ],
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
