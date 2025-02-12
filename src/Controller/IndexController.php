<?php
/**
 * Этот файл является частью модуля веб-приложения GearMagic.
 * 
 * @link https://gearmagic.ru
 * @copyright Copyright (c) 2015 Веб-студия GearMagic
 * @license https://gearmagic.ru/license/
 */

namespace Gm\Backend\Recovery\Controller;

use Gm;
use Gm\Helper\Url;
use Gm\Panel\Http\Response;
use Gm\Mvc\Controller\Controller;

/**
 * Контроллер восстановления аккаунта пользователя.
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Gm\Backend\Recovery\Controller
 * @see 4.0
 */
class IndexController extends Controller
{
    /**
     * {@inheritDoc}
     */
    protected string $defaultModel = 'Form';

    /**
     * {@inheritdoc}
     */
    public function behaviors(): array
    {
        $behaviors = [
            'verb' => [
                'class'    => '\Gm\Filter\VerbFilter',
                'autoInit' => true,
                'actions'  => [
                    ''       => 'GET',
                    'verify' => ['POST', 'ajax' => 'GJAX']
                ]
            ]
        ];

        // настройки модуля
        $settings = $this->module->getSettings();
        // если в настройках установлен флаг "запись действий в журнал аудита"
        if ($settings->auditWrite) {
            $behaviors['audit'] = [
                'class'    => '\Gm\Panel\Behavior\AuditBehavior',
                'autoInit' => true,
                'allowed'  => 'verify',
                // изменение шаблона комментария в зависимости от успеха восстановления
                'commentCallback' => function () {
                    /** @var \Gm\Panel\Audit\Info $this */
                    if ($this->success) {
                        $comment = 'User {user} applied for account recovery for {email} from module {module} in {date} from the IP address {ipaddress}, of the operating system {os} in the browser {browser}';
                    } else {
                        $comment = 'User applied for account recovery for {email} from module {module} in {date} from the IP address {ipaddress}, of the operating system {os} in the browser {browser}';
                        $this->error = $this->error ? strip_tags($this->error) : null;
                    }

                    /** @var array $params параметры замены комментария */
                    $params = [
                        '@incut',
                        'user'      => $this->unknown,
                        'module'    => $this->moduleName ?: $this->unknown,
                        'date'      => $this->date ? Gm::$app->formatter->toDateTime($this->date) . ' (' . Gm::$app->formatter->timeZone->getName() . ')' : $this->unknown,
                        'ipaddress' => $this->ipaddress,
                        'browser'   => $this->getBrowserName(),
                        'os'        => $this->getOsName(),
                        'error'     => $this->error ?: $this->unknown
                    ];
                    /** @var \Gm\Backend\Recovery\Model\Form $model */
                    if ($model = Gm::$app->controller->getLastModel()) {
                        $params['email'] = $model->email;
                        if ($this->success) {
                            $this->userId   = $model->identity->id;
                            $this->userName = $model->identity->username;
                            $params['user'] = $model->identity->username;
                            /** @var \Gm\Panel\User\UserProfile $profile */
                            $profile = $model->identity->getProfile();
                            if ($profile) {
                                $this->userDetail  = $profile->name;
                                $params['profile'] = $profile->name ?: $this->unknown;
                            }
                        }
                    }
                    $comment = Gm::$app->module->t($comment, $params);
                    if ($model && $this->success) {
                        // если письмо отправлено пользователю для восстановления
                        if ($model->mailSentToUser) {
                            $comment .= '. ' . Gm::$app->module->t(
                                'The procedure for recovering access (password reset) to the account was sent to the user {user} by email {email}',
                                [
                                    'user'  => $model->identity->username ?: $this->unknown,
                                    'email' => $model->email
                                ]
                            ) . '. ';
                        }
                        if ($model->mailSent) {
                            // настройки модуля
                            $settings =  Gm::$app->module->getSettings();
                            $comment .= Gm::$app->module->t(
                                'For control, information sent to email {email}',
                                [
                                    'email' => $settings->mail
                                ]
                            ) . '. ';
                        }
                    }
                    return $comment;
                }
            ];
        }
        return $behaviors;
    }

    /**
     * Действие "index" выводит страницу восстановления аккаунта.
     * 
     * @return string
     */
    public function indexAction(): string
    {
        $app = Gm::$app;

        // если запрос из панели управления, перенаправляем обратно, но уже с командой
        if ($app->request->isGjax()) {
            return $this->getResponse(Response::FORMAT_JSONG)
                ->meta
                    ->cmdRedirect(Url::to(Gm::alias('@route')));
        }

        // при обращении к странице авторизации, пользователя перенаправляет в панель управления
        if (Gm::hasUserIdentity(BACKEND_SIDE_INDEX)) {
            return $this->getResponse()->redirect(Gm::alias('@backend', '/workspace'), false);
        }

        // загаловок страницы
        $app->page
            ->setTitle($this->t('Recovery page'))
            ->script
                ->meta->csrfTokenTag();

        // для восстановления
        $url = ['recovery/verify'];

        /** @var $version \Gm\Version\AppVersion */
        $version = $app->version;
        /** @var $edition  \Gm\Version\Edition */
        $edition = $app->version->getEdition();

        return $this->render('/form', [
            'settings'  => $this->module->getSettings(),
            'appTitle'  => $app->language->isRu() ? $version->originalName : $version->name,
            'formTitle' => $app->language->isRu() ? $edition->originalName : $edition->name,
            'formUrl'   => Url::toBackend($url)
        ], [
            'useTheme'    => true, 
            'useLocalize' => true
        ]);
    }

    /**
     * Действие "verify" выполняет проверку аккаунта пользователя.
     * 
     * @return Response
     */
    public function verifyAction(): Response
    {
        /** @var Response $response */
        $response = $this->getResponse(Response::FORMAT_JSONG);
        /** @var \Gm\Http\Request $request */
        $request = Gm::$app->request;

        /** @var \Gm\Backend\Recovery\Model\Form $form Модель данных формы */
        $form = $this->getModel($this->defaultModel);
        if ($form === false) {
            $response
                ->meta->error(Gm::t('app', 'Could not defined data model "{0}"', [$this->defaultModel]));
            return $response;
        }

        // загрузка атрибутов в модель из запроса
        if (!$form->load($request->getPost())) {
            $response
                ->meta->error(Gm::t(BACKEND, 'No data to perform action'));
            return $response;
        }

        // валидация атрибутов модели
        if (!$form->validate()) {
            $response
                ->meta->error($this->t($form->getError()));
            return $response;
        }

        // попытка восстановления аккаунта
        if (($result = $form->recovery()) !== true) {
            $response->setStatusCode(503);
            $response
                ->meta->error($this->t($result));
            return $response;
        }

        $response
            ->meta->success($this->t('Account recovery instructions sent to your inbox'));
        return $response;
    }
}
