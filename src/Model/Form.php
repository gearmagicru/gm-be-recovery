<?php
/**
 * Этот файл является частью модуля веб-приложения GearMagic.
 * 
 * @link https://gearmagic.ru
 * @copyright Copyright (c) 2015 Веб-студия GearMagic
 * @license https://gearmagic.ru/license/
 */

namespace Gm\Backend\Recovery\Model;

use Gm;
use Gm\Data\Model\RecordModel;
use Gm\Panel\User\UserIdentity;
use Gm\Panel\Version\Version as GPanelVersion;

/**
 * Модель восстановления аккаунта пользователя.
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Gm\Backend\Recovery\Model
 * @since 1.0
 */
class Form extends RecordModel
{
    /**
     * Информация о пользователе.
     * 
     * * @see Form::getUser()
     * 
     * @var UserIdentity|null
     */
    public ?UserIdentity $identity = null;

    /**
     * E-mail успешно отправлен пользователю.
     * 
     * @see Form::recovery()
     * 
     * @var bool
     */
    public bool $mailSentToUser = false;

    /**
     * E-mail успешно отправлен уведомителю (администратору).
     * 
     * @see Form::recovery()
     * 
     * @var bool
     */
    public bool $mailSent = false;

    /**
     * {@inheritdoc}
     */
    public function getModelName(): string
    {
        return 'recovery';
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'captcha' => $this->t('Captcha'),
            'email'   => 'E-mail'
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function validationRules(): array
    {
        $rules = [
            'checkEmpty' => [['email'], 'notEmpty'],
            'checkEmail' => [['email'], 'filter', 'filter' => FILTER_VALIDATE_EMAIL]
        ];
        $settings = $this->module->getSettings();
        if ($settings->captcha) {
            $rules['checkEmpty'][0][]  = 'captcha';
            $rules['checkCaptcha'] = [['captcha'], 'kcaptcha', 'param' => 'kcaptcha'];
        }
        return $rules;
    }

    /**
     * {@inheritdoc}
     */
    public function afterValidate(bool $isValid): bool
    {
        if ($isValid) {
            $this->identity = Gm::$app->user->createIdentity();
        }
        return $isValid;
    }

    /**
     * Проверяет, существует ли пользователь с указаннным e-mail.
     * 
     * @param string $email E-mail пользователя.
     * 
     * @return UserIdentity|null Объект идентификации, соответствующий данному идентификатору 
     *     или условию поиска. Если null, пользователь не найден.
     */
    public function getUser(string $email): ?UserIdentity
    {
        /** @var \Gm\Panel\User\UserProfile $profile */
        $profile = $this->identity->createProfile()->findOne(['email' => $email]);
        if ($profile !== null) {
            $user = $this->identity->findIdentity(['id' => $profile['user_id']]);
            if ($user !== null) {
                // если аккаунт пользователя активен и пользователь может проходить авторизацию, 
                // как на backend, так и на frontend
                if (($user->side == BACKEND_SIDE_INDEX || $user->side == BOTH_SIDE_INDEX) && $user->status ==  Gm::$app->user::STATUS_ACTIVE) {
                    return $user;
                }
            }
        }
        return null;
    }

    /**
     * Возвращает имя файла шаблона письма.
     * 
     * @param \Gm\Config\Config $settings Настройки модуля.
     * @param bool $forUser Шаблона письма предназначен для пользователя, иначе для 
     *     уведомителя (администратора).
     * 
     * @return false|string Если false, указанный в настройках модуля шаблон не существует.
     */
    protected function getMailTemplateFile($settings, bool $forUser = true): false|string
    {
        $template = $forUser ? $settings->templateUserMail : $settings->templateMail;
        if (empty($template)) {
            return false;
        }

        if ($template === 'default') {
            return $forUser ? 'mails/recovery-user' : 'mails/recovery';
        }
        return Gm::$app->theme->getTemplateFile($template, false);
    }

    /**
     * Отправляет письмо.
     * 
     * @param string $subject Тема письма.
     * @param string $to Кому назначается (указывается через ",").
     * @param string $template Имя шаблона или имя файла шаблона письма.
     * @param array $variables Переменные в шаблоне письма.
     * 
     * @return bool|string Если `true`, письмо успешно отправлено, иначе сообщение об ошибке.
     */
    protected function sendMail(string $subject, string $to, string $template, array $variables  = []): bool|string
    {
        // шаблон письма
        $view = Gm::$app->getView();
        $variables['version'] = Gm::$app->version; // версия платформы
        $variables['date']    = Gm::$app->formatter->toDateTime('now') . ' (' . Gm::$app->formatter->timeZone->getName() . ')';
        // параметры письма
        $options = [
            'body'    => $view->render($template, $variables),
            'subject' => $subject
        ];
        // адрес "Кому"
        $to = explode(',', $to);
        $addressTo = [];
        foreach ($to as $address) {
            $addressTo[] = ['address' => trim($address)];
        }
        if (sizeof($to) > 1) {
            $options['cc'] = $addressTo;
        } else 
            $options['to'] = $addressTo;
        Gm::$app->mail->isHtml = true;
        return Gm::sendMail($options, true);
    }

    /**
     * Проверяет аккаунт пользователя и добавляет запись для восстановления.
     *
     * @return bool|string Возвращает `true` после выполнении восстановления аккаунта 
     *     или `string` в случае возникновения ошибки.
     */
    public function recovery(): bool|string
    {
        // если пользователь авторизован и пытается восстановить аккаунт
        if (Gm::hasUserIdentity(BACKEND_SIDE_INDEX)) {
            return 'To recovery your account, you need to log out of the control panel';
        }

        // проверка аккаунта пользователя
        $user = $this->getUser($this->email);
        if ($user === null) {
            return 'User with specified email not found';
        }

        /** @var \Gm\Backend\Recovery\Helper\Helper $helper */
        $helper = $this->module->getHelper();
        // удаляем запись ранее добавленную для пользователя
        $helper->resetUserRecovery((int) $user->id);
        $helper->appendUserRecovery((int) $user->id, $this->email);

        // URL-адрес для восстановления аккаунта
        $recoveryUrl = $helper->getRecoveryMailUrl();
        // настройки модуля
        $settings = $this->module->getSettings();

        // Отправить письмо получателю (администратору), если в настройках 
        // установлен флаг "Отправить письмо на e-mail: получателю уведомлений" и указан его e-mail
        if ($settings->sendMail) {
            // шаблон письма
            $template = $this->getMailTemplateFile($settings, false);
            if ($settings->mail && $template !== false) {
                $this->mailSent = $sent = $this->sendMail(
                    $this->module->t('User account recovery «{0}»', [Gm::$app->version->name]),
                    $settings->mail,
                    $template,
                    [
                        'recoveryUrl'    => $recoveryUrl,
                        'email'          => $this->email,
                        'password'       => $helper->getRecoveryPassword(),
                        'user'           => $user,
                        'profile'        => $user->getProfile(),
                        'device'         => $user->getDevice(),
                        'editionVersion' => Gm::$app->version->getEdition(), // версия редакции
                        'panelVersion'   => new GPanelVersion() // версия панели управления
                    ]
                );
                if ($this->mailSent !== true) {
                    Gm::error($this->mailSent);
                }
            }
        }

        // Отправить письмо пользователю
        $template = $this->getMailTemplateFile($settings, true);
        $sent     = false;
        if ($template !== false) {
            $this->mailSentToUser = $sent = $this->sendMail(
                $this->module->t('User account recovery «{0}»', [Gm::$app->version->name]),
                $this->email,
                $template,
                [
                    'recoveryUrl'    => $recoveryUrl,
                    'email'          => $this->email,
                    'password'       => $helper->getRecoveryPassword(),
                    'user'           => $user,
                    'profile'        => $user->getProfile(),
                    'device'         => $user->getDevice(),
                    'editionVersion' => Gm::$app->version->getEdition(), // версия редакции
                ]
            );
            if ($this->mailSentToUser !== true) {
                Gm::error($this->mailSentToUser);
            }
        }

        if ($sent !== true) {
            return 'Account recovery error';
        }
        return true;
    }
}
