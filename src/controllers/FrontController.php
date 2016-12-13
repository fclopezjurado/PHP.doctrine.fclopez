<?php

/**
 * Created by PhpStorm.
 * User: fran lopez
 * Date: 10/12/2016
 * Time: 17:32
 */

namespace MiW16\Results\Controllers;

use Symfony\Component\Routing\Loader\YamlFileLoader;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

require_once __DIR__ . '/../../bootstrap.php';

$frontController = new FrontController();
$frontController->processRequest();

class FrontController
{
    const ROUTES_FILE = 'routes.yml';
    const ROUTES_DIR = __DIR__ . '/../config/';
    const RESOURCE_COULD_NOT_BE_FOUND = 'The resource could not be found';
    const FORBIDDEN_RESOURCE = 'The resource was found but the request method is not allowed';
    const BAD_REQUEST = 'Bad request';
    const INTERNAL_SERVER_ERROR = 'Internal server error';

    const ERROR_RESPONSE_ATTRIBUTE = 'error';
    const MESSAGE_RESPONSE_ATTRIBUTE = 'message';
    const CODE_RESPONSE_ATTRIBUTE = 'code';

    const CONTROLLER_URI_ATTRIBUTE = 'controller';
    const CONTROLLER_ACTION_URI_ATTRIBUTE = 'action';

    private $routes;
    private $matcher;
    private $controllers;

    /**
     * FrontController constructor.
     */
    public function __construct()
    {
        $locator = new FileLocator(array(FrontController::ROUTES_DIR));
        $loader  = new YamlFileLoader($locator);
        $context = new RequestContext(filter_input(INPUT_SERVER, 'REQUEST_URI'));

        $this->routes = $loader->load(FrontController::ROUTES_FILE);
        $this->matcher = new UrlMatcher($this->routes, $context);
        $this->controllers = array(new ResultController(), new UserController());
    }

    public function processRequest()
    {
        $pathInfo = filter_input(INPUT_SERVER, 'PATH_INFO');

        try {
            if (is_null($pathInfo)) {
                $response = $this->generateResponseBody(Response::HTTP_BAD_REQUEST, true, FrontController::BAD_REQUEST);
                $response->send();
            }
            else {
                $URIParameters = $this->matcher->match($pathInfo);

                if (is_array($URIParameters)) {
                    foreach ($this->controllers as $controller) {
                        $controllerName = $URIParameters[FrontController::CONTROLLER_URI_ATTRIBUTE];
                        $controllerAction = $URIParameters[FrontController::CONTROLLER_ACTION_URI_ATTRIBUTE];

                        if (preg_match("/$controllerName/", get_class($controller))) {
                            /**
                             * @var Response $response
                             */
                            $response = $controller->$controllerAction($this, $URIParameters);
                            $response->send();
                        }
                    }
                }
                else {
                    $response = $this->generateResponseBody(Response::HTTP_INTERNAL_SERVER_ERROR, true,
                        FrontController::INTERNAL_SERVER_ERROR);
                    $response->send();
                }
            }
        } catch (ResourceNotFoundException $e) {
            $response = $this->generateResponseBody(Response::HTTP_NOT_FOUND, true,
                FrontController::RESOURCE_COULD_NOT_BE_FOUND);
            $response->send();
        } catch (MethodNotAllowedException $e) {
            $response = $this->generateResponseBody(Response::HTTP_FORBIDDEN, true,
                FrontController::FORBIDDEN_RESOURCE);
            $response->send();
        }
    }

    /**
     * @param $statusCode
     * @param $error
     * @param $message
     * @return JsonResponse
     */
    public function generateResponseBody($statusCode, $error, $message)
    {
        $response = new JsonResponse();
        $response->setStatusCode($statusCode);
        $response->setData(array(
            FrontController::ERROR_RESPONSE_ATTRIBUTE => $error,
            FrontController::MESSAGE_RESPONSE_ATTRIBUTE => $message,
            FrontController::CODE_RESPONSE_ATTRIBUTE => $statusCode
        ));

        return $response;
    }
}
