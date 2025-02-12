<?php
/**
 * Этот файл является частью модуля веб-приложения GearMagic.
 * 
 * Пакет английской (британской) локализации.
 * 
 * @link https://gearmagic.ru
 * @copyright Copyright (c) 2015 Веб-студия GearMagic
 * @license https://gearmagic.ru/license/
 */

return [
    '{name}'        => 'Account recovery',
    '{description}' => 'Restoring access to your Control Panel account',
    '{permissions}' => [],

    // Settings
    '{settings.title}' => 'Module settings',
    // Settings: поля
    'default' => 'default',
    'This is a generic layout that is used to send messages to notification recipients' 
        => 'This is a generic layout that is used to send messages to notification recipients',
    'for users' => 'for users',
    'This is a generic layout that is used to send messages to users' 
        => 'This is a generic layout that is used to send messages to users',

    // Index: шаблон
    'Title' => 'account recovery',
    'Title recovery account' => 'Account recovery',
    'Recover password' => 'Password recovery',
    'Code' => 'Code',
    'Refresh code' => 'Refresh code',
    'Enter the email' => 'Enter the email',
    'Enter the code' => 'Enter the code',
    'Recovery' => 'Recovery',
    'All right reserved' => 'All right reserve.',
    'version' => 'version',
    'Signin page' => 'Signin page',
    'Recovery page' => 'Recovery page',
    'Recovery' => 'Recovery',
    'Captcha' => 'Captcha code',
    // Index: шаблон / сообщения
    'The value of the field {0} contains invalid characters' => 'The value of the field "{0}" contains invalid characters.',
    'The length of the field value {0} must be between {1} and {2} characters' => 'The length of the field value "{0}" must be between {1} and {2} characters.',
    'The value of the field {0} must be less than {1} characters' => 'The value of the field "{0}" must be less than {1} characters.',
    'The value of the field {0} must be greater than {1} characters' => 'The value of the field "{0}" must be greater than {1} characters.',
    'You did not fill in the field {0}' => 'You did not fill in the field "{0}"!',
    // Index: сообщения
    'Account recovery instructions sent to your inbox' => 'Account recovery instructions sent to your inbox.',
    // Index: журнал аудита
    'User {user} applied for account recovery for {email} from module {module} in {date} from the IP address {ipaddress}, of the operating system {os} in the browser {browser}' 
        => 'User «<b>{user}</b>» applied for account recovery for «<b>{email}</b>» from module «<b>{module}</b>» in<b>{date}</b> from the IP address <b>{ipaddress}</b>, of the operating system «<b>{os}</b>» in the browser «<b>{browser}</b>»',
    'User applied for account recovery for {email} from module {module} in {date} from the IP address {ipaddress}, of the operating system {os} in the browser {browser}' 
        => 'User applied for account recovery for «<b>{email}</b>» from module «<b>{module}</b>» in <b>{date}</b> from the IP address <b>{ipaddress}</b>, of the operating system «<b>{os}</b>» in the browser «<b>{browser}</b», but got an error «{error}»',
    'The procedure for recovering access (password reset) to the account was sent to the user {user} by email {email}'
        => 'The procedure for recovering access (password reset) to the account was sent to the user «<b>{user}</b>» by email «<b>{email}</b>»',
    'For control, information sent to email {email}' => 'For control, information sent to email «<b>{email}</b>»',

    // Form
    'User account recovery «{0}»' => 'User account recovery «{0}»',
    // Form: сообщения
    'User with specified email not found' => 'User with specified email not found.',
    'To recovery your account, you need to log out of the control panel' => 'To recovery your account, you need to log out of the control panel.',
    'Account recovery error' => 'Account recovery error.'
];
