<?php
/**
 * Этот файл является частью модуля веб-приложения GearMagic.
 * 
 * Пакет русской локализации.
 * 
 * @link https://gearmagic.ru
 * @copyright Copyright (c) 2015 Веб-студия GearMagic
 * @license https://gearmagic.ru/license/
 */

return [
    '{name}'        => 'Восстановление учетной записи (аккаунта)',
    '{description}' => 'Восстановление доступа к  учетной записи (аккаунту) Панели управления',
    '{permissions}' => [],

    // Settings
    '{settings.title}' => 'Настройка модуля',
    // Settings: поля
    'default' => 'по умолчанию',
    'for notification recipients' => 'для получателей уведомлений',
    'This is a generic layout that is used to send messages to notification recipients' 
        => 'Это универсальный макет, который используется для рассылки сообщений получателей уведомлений',
    'for users' => 'для пользователей',
    'This is a generic layout that is used to send messages to users' 
        => 'Это универсальный макет, который используется для рассылки сообщений пользователям',

    // Index: шаблон
    'Title' => 'восстановление аккаунта',
    'Title recovery account' => 'Восстановление аккаунта',
    'Recover password' => 'Восcтановление пароля',
    'Code' => 'Код капчи',
    'Refresh code' => 'Обновить капчу',
    'Enter the email' => 'Введите e-mail адрес',
    'Enter the code' => 'Введите код капчи',
    'Recovery' => 'Восстановить',
    'All right reserved' => 'Все права защищены.',
    'version' => 'версия',
    'Signin page' => 'Авторизация пользователя',
    'Recovery page' => 'Восстановление аккаунта',
    'Recovery' => 'Восстановить',
    'Captcha' => 'Код капчи',
    // Index: шаблон / сообщения
    'The value of the field {0} contains invalid characters' => 'Значение поля "{0}", содержит некорректные символы.',
    'The length of the field value {0} must be between {1} and {2} characters' => 'Длина значения поля "{0}", должна быть от {1} до {2} символов.',
    'The value of the field {0} must be less than {1} characters' => 'Длина значения поля "{0}", должна быть меньше {1} символов.',
    'The value of the field {0} must be greater than {1} characters' => 'Длина значения поля "{0}", должна быть больше {1} символов.',
    'You did not fill in the field {0}' => 'Вы не заполнили поле "{0}"!',
    // Index: сообщения
    'Account recovery instructions sent to your inbox' => 'На ваш почтовый ящик отправлена инструкция по восстановлению аккаунта.',
    // Index: журнал аудита
    'User {user} applied for account recovery for {email} from module {module} in {date} from the IP address {ipaddress}, of the operating system {os} in the browser {browser}' 
        => 'Пользователь «<b>{user}</b>» подал заявку на восстановление аккаунта для email «<b>{email}</b>» из модуля «<b>{module}</b>» в <b>{date}</b> c IP-адреса <b>{ipaddress}</b>, операционной системы «<b>{os}</b>» в браузере «<b>{browser}</b>»',
    'User applied for account recovery for {email} from module {module} in {date} from the IP address {ipaddress}, of the operating system {os} in the browser {browser}' 
        => 'Пользователь пытался подать заявку на восстановление аккаунта (сброса пароля) для email «<b>{email}</b>» из модуля «<b>{module}</b>» в <b>{date}</b> c IP-адреса <b>{ipaddress}</b>, операционной системы «<b>{os}</b>» в браузере «<b>{browser}</b>», но получил ошибку «{error}»',
    'The procedure for recovering access (password reset) to the account was sent to the user {user} by email {email}'
        =>  'Порядок восстановления доступа (сброса пароля) к аккаунту был отправлен пользователю «<b>{user}</b>» на email «<b>{email}</b>»',
    'For control, information sent to email {email}' => 'Для контроля, информация отправлена на email «<b>{email}</b>»',

    // Form
    'User account recovery «{0}»' => 'Восстановление аккаунта пользователя «{0}»',
    // Form: сообщения
    'User with specified email not found' => 'Пользователь с указанным email не найден.',
    'To recovery your account, you need to log out of the control panel' => 'Для восстановления аккаунта, вам необходимо выйти из панели управления.',
    'Account recovery error' => 'Ошибка восстановления аккаунта.'
];
