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
use Gm\Panel\Data\Model\ModuleSettingsModel;

/**
 * Модель настроек модуля.
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Gm\Backend\Recovery\Model
 * @since 1.0
 */
class Settings extends ModuleSettingsModel
{
    /**
     * {@inheritdoc}
     */
    public function init(): void
    {
        parent::init();

        $this
            ->on(self::EVENT_AFTER_SAVE, function ($isInsert, $columns, $result, $message) {
                // всплывающие сообщение
                $this->response()
                    ->meta
                        ->cmdPopupMsg(Gm::t(BACKEND, 'Settings successfully changed'), $this->t('{settings.title}'), 'accept');
            });
    }

    /**
     * {@inheritdoc}
     */
    public function getDirtyAttributes(array $names = null): array
    {
        return $this->attributes;
    }

    /**
     * {@inheritdoc}
     */
    public function maskedAttributes(): array
    {
        return [
            'mail'             => 'mail', // e-mail получателя уведомлений
            'templateUserMail' => 'templateUserMail', // шаблон письма пользователя
            'templateMail'     => 'templateMail', // шаблон письма получателя уведомлений
            'sendMail'         => 'sendMail', // отправить письмо на e-mail получателю уведомлений
            'captcha'          => 'captcha', // задействовать капчу
            'auditWrite'       => 'auditWrite' // запись действий в журнал аудита
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'mail'             => 'Notification recipient\'s email',
            'templateMail'     => 'for notification recipients',
            'templateUserMail' => 'for users'
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function formatterRules(): array
    {
        return [
            [
                ['captcha', 'auditWrite', 'sendMail'], 'logic' => [true, false],
            ]
        ];
    }

    /**
     * @param \Gm\Theme\Info\ViewsInfo $views
     * @param string $name
     * 
     * @return bool
     */
    protected function templateValidate($viewsInfo, string $name): bool
    {
        $view = $this->attributes[$name] ?? null;
        // если шаблон не по умолчанию, тогда проверяем все шаблоны писем текущей темы
        if ($view && $view !== 'default') {
            $info = $viewsInfo->getBy('view', $view);
            if ($info === null || !Gm::$app->theme->templateExists($info['filename'])) {
                $this->addError($this->t('The description of the email template you selected does not exist or is not in the current subject, this template does not exist'));
                return false;
            }
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function afterValidate(bool $isValid): bool
    {
        if ($isValid) {
            // проверка шаблоном писем, т.к. тема может поменяться, а там не будет этих шаблонов
            $views = Gm::$app->createBackendTheme()->getViewsInfo();
            // загружаем описание шаблонов текущей темы
            $views->load();
            // проверяем все шаблоны писем текущей темы
            if (!$this->templateValidate($views, 'templateUserMail')) return false;
            if (!$this->templateValidate($views, 'templateMail')) return false;
        }
        return $isValid;
    }
}
