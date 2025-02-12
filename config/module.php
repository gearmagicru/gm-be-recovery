<?php
/**
 * Этот файл является частью модуля веб-приложения GearMagic.
 * 
 * Файл конфигурации модуля.
 * 
 * @link https://gearmagic.ru
 * @copyright Copyright (c) 2015 Веб-студия GearMagic
 * @license https://gearmagic.ru/license/
 */

return [
    'translator' => [
        'locale'   => 'auto',
        'patterns' => [
            'text' => [
                'basePath' => __DIR__ . '/../lang',
                'pattern'  => 'text-%s.php'
            ]
        ],
        'autoload' => ['text'],
        'external' => [BACKEND]
    ],

    'accessRules' => [
        // для всех пользователей
        [
            'allow',
            'controllers' => ['Index', 'Captcha'],
        ],
        // для авторизованных пользователей Панели управления
        [ // разрешение "Информация о модуле" (info)
            'allow',
            'controllers' => ['Info'],
            'permission'  => 'info',
            'users'       => ['@backend']
        ],
        [ // разрешение "Настройка модуля" (settings)
            'allow',
            'controllers' => ['Settings'],
            'permission'  => 'settings',
            'users'       => ['@backend']
        ],
        [ // для всех остальных, доступа нет
            'deny',
            'exception' => 404
        ]
    ],

    'dataManager' => [
        'recovery' => [
            'tableName'  => '{{user_recovery}}',
            'primaryKey' => 'id',
            'fields'     => [
                ['user_id'],
                ['date'],
                ['password'],
                ['email', 'title' => 'E-mail'],
                ['captcha', 'title' => 'Captcha'],
            ],
            'useAudit' => false
        ]
    ],

    'viewManager' => [
        'id'          => 'gm-recovery-{name}',
        'useTheme'    => true,
        'useLocalize' => true,
        'viewMap'     => [
            // информации о модуле
            'info' => [
                'viewFile'      => '//backend/module-info.phtml', 
                'forceLocalize' => true
            ],
            'recovery'         => '/layouts/recovery.phtml', // макет страницы восстановления
            'main'             => '/form.phtml',
            'form'             => '/form.phtml', // форма восстановления
            'mailRecovery'     => '/mails/recovery.phtml', // письмо пользователю
            'mailUserRecovery' => '/mails/recovery-user.phtml', // письмо уведомителю (администратору)
        ]
    ]
];
