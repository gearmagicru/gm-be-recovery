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
use Gm\Http\Response;
use Gm\Mvc\Controller\Controller;

/**
 * Контроллер вывода капчи.
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Gm\Backend\Recovery\Controller
 * @see 4.0
 */
class Captcha extends Controller
{
    /**
     * Действие "index" выводит капчу.
     * 
     * @return Response
     */
    public function indexAction(): Response
    {
        /** @var Response $response */
        $response = $this->getResponse();
        
        $widget = Gm::$app->widgets->get('gm.wd.kcaptcha', ['toString' => true]);
        if ($widget) {
            /** @var string $type Тип изображения */
            $type = $widget->getCaptcha()->getType();
            if ($type) {
                /** @var \Gm\Http\Headers $headers */
                $headers = $response->getHeaders();
                $headers->add('content-type', 'image/' . $type);
                $response->setContent($widget->run());
            }
        }
        return $response; 
    }
}
