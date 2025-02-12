<?php
/**
 * Этот файл является частью модуля веб-приложения GearMagic.
 * 
 * Файл конфигурации установки модуля.
 * 
 * @link https://gearmagic.ru
 * @copyright Copyright (c) 2015 Веб-студия GearMagic
 * @license https://gearmagic.ru/license/
 */

return [
    'use'         => BACKEND,
    'id'          => 'gm.be.recovery',
    'name'        => 'Account recovery',
    'description' => 'Recovering access to your Control Panel account',
    'namespace'   => 'Gm\Backend\Recovery',
    'path'        => '/gm/gm.be.recovery',
    'route'       => 'recovery',
    'routes'      => [
        [
            'type'    => 'crudSegments',
            'options' => [
                'module'      => 'gm.be.recovery',
                'route'       => 'recovery',
                'prefix'      => BACKEND,
                'childRoutes' => [
                    'verify' => [
                        'route'    => 'verify',
                        'defaults' => ['action' => 'verify']
                    ]
                ]
            ]
        ]
    ],
    'locales'     => ['ru_RU', 'en_GB'],
    'permissions' => ['settings', 'info'],
    'events'      => [],
    'required'    => [
        ['php', 'version' => '8.2'],
        ['app', 'code' => 'GM MS'],
        ['app', 'code' => 'GM CMS'],
        ['app', 'code' => 'GM CRM'],
    ]
];
