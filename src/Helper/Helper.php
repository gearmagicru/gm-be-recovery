<?php
/**
 * Этот файл является частью модуля веб-приложения GearMagic.
 * 
 * @link https://gearmagic.ru
 * @copyright Copyright (c) 2015 Веб-студия GearMagic
 * @license https://gearmagic.ru/license/
 */

namespace Gm\Backend\Recovery\Helper;

use Gm;
use Gm\Helper\Str;
use Gm\Helper\Url;
use Gm\Data\Model\BaseModel;

/**
 * Помощник восстановления аккаунта.
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Gm\Backend\Recovery\Helper
 * @since 1.0
 */
class Helper extends BaseModel
{
    /**
     * Имя токена в записи восстановления аккаунта.
     * 
     * @var string
     */
    public string $recoveryTokenName = 'recoveryToken';

    /**
     * Длина генерируемого токена в записи восстановления аккаунта.
     * 
     * @var int
     */
    public int $recoveryTokenLength = 128;

    /**
     * Длина генерируемого пароля пользователя.
     * 
     * @var int
     */
    public int $recoveryPasswordLength = 6;

    /**
     * Сгенерированный токен в записи восстановления аккаунта.
     * 
     * @see Helper::getRecoveryToken()
     * 
     * @var string
     */
    protected string $recoveryToken;

    /**
     * Сгенерированный пароль пользователя.
     * 
     * @see Helper::getRecoveryPassword()
     * 
     * @var string
     */
    protected string $recoveryPassword;

    /**
     * Возвращает URL-адрес для восстановления аккаунта.
     * 
     * URL-адрес добавляется в шаблон письма с инструкциями восстановления аккаунта.
     * 
     * @return string
     */
    public function getRecoveryMailUrl(): string
    {
        return Url::toBackend(['', '?' => [
            $this->recoveryTokenName => $this->getRecoveryToken(),
            'from' => 'mail']
        ]);
    }

    /**
     * Генерирует пароль для восстановления аккаунта пользователя.
     * 
     * @param int|null $length Длина генерируемого пароль. Если значение `null`,
     *     то длина пароля {@see Helper::$recoveryPasswordLength}.
     * 
     * @return string Пароль для восстановления аккаунта пользователя.
     */
    public function generateRecoveryPassword(int $length = null): string
    {
        return Gm::$app->encrypter->generatePassword($length ?: $this->recoveryPasswordLength);
    }

    /**
     * Возвращает (генерирует) пароль для восстановления аккаунта пользователя.
     * 
     * @see Helper::generateRecoveryPassword()
     * 
     * @return string Пароль для восстановления аккаунта пользователя.
     */
    public function getRecoveryPassword(): string
    {
        if (!isset($this->recoveryPassword)) {
            $this->recoveryPassword = $this->generateRecoveryPassword();
        }
        return $this->recoveryPassword;
    }

    /**
     * Генерирует токен записи восстановления аккаунта пользователя.
     * 
     * @param int|null $length Длина генерируемого токена. Если значение `null`,
     *     то длина токена {@see Helper::$recoveryTokenLength}.
     * 
     * @return string Токен записи восстановления аккаунта пользователя.
     */
    public function generateRecoveryToken(int $length = null): string
    {
        return Str::random($length ?: $this->recoveryTokenLength);
    }

    /**
     * Возвращает (генерирует) токен записи восстановления аккаунта пользователя.
     * 
     * @see Helper::generateRecoveryToken()
     * 
     * @return string Токен записи восстановления аккаунта пользователя.
     */
    public function getRecoveryToken(): string
    {
        if (!isset($this->recoveryToken)) {
            $this->recoveryToken = $this->generateRecoveryToken();
        }
        return $this->recoveryToken;
    }

    /**
     * Возвращает имя токен записи восстановления аккаунта пользователя.
     * 
     * @see Helper::$recoveryTokenName
     * 
     * @return string Имя токена записи восстановления аккаунта пользователя.
     */
    public function getRecoveryTokenName(): ?string
    {
        return $this->recoveryTokenName;
    }

    /**
     * Добавляет запись в таблицу для восстановления аккаунта пользователя.
     * 
     * @param int|string $id Идентификатор пользователя.
     * @param string $email E-mail пользователя.
     * @param string $token Токен.
     * @param string $password Пароль.
     * 
     * @return int|string Идентификатор добавленной записи.
     */
    public function appendUserRecovery(int $userId, string $email, ?string $password = null, ?string $token = null): int|string
    {
        if ($password === null) {
            $password = $this->getRecoveryPassword();
        }
        if ($token === null) {
            $token = $this->getRecoveryToken();
        }
        return $this->insertRecord(
            [
                'user_id'  => $userId,
                'email'    => $email,
                'token'    => $token,
                'date'     => gmdate('Y-m-d H:i:s'),
                'password' => Gm::$app->encrypter->encodePassword($password)
            ],
            '{{user_recovery}}'
        );
    }

    /**
     * Сбрасывает запись восстановления аккаунта пользователя.
     * 
     * @param int $userId Идентификатор пользователя.
     * 
     * @return false|int Возвращает количество удаленных записей или false в случае 
     *     возникновения ошибки.
     */
    public function resetUserRecovery(int $userId): false|int
    {
        return $this->deleteRecord(['user_id' => $userId], '{{user_recovery}}');
    }

    /**
     * Возвращает запись восстановления аккаунта пользователя.
     * 
     * @param array $condition Условия запроса.
     * 
     * @return array|null
     */
    public function getUserRecovery(array $condition): ?array
    {
        /** @var \Gm\Db\Adapter\Driver\AbstractCommand $command */
        $command = $this->getDb()->createCommand();
        $command->select('{{user_recovery}}', ['*'], $condition);
        return $command->queryOne();
    }
}
