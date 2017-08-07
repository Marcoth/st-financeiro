<?php

declare(strict_types=1);

namespace STFin\Plugins;

use Aura\Router\RouterContainer;
use Interop\Container\ContainerInterface;
use Psr\Http\Message\RequestInterface;
use STFin\ServiceContainerInterface;
use STFin\Plugins\PluginInterface;
use Zend\Diactoros\ServerRequestFactory;

class RoutePlugin implements PluginInterface
{
    /**
     * registra as rotas da aplicação
     */
    public function register(ServiceContainerInterface $container)
    {
        $routerContainer = new RouterContainer();
        /**
         * @var $map registrar as rotas da aplicação
         */
        $map = $routerContainer->getMap();
        /**
         * @var $matcher idenfitica a rota que está sendo acessada
         */
        $matcher = $routerContainer->getMatcher();
        /**
         * @var $generator gerar links com base nas rotas registradas
         */
        $generator = $routerContainer->getGenerator();
        $request = $this->getRequest();
        /**
         * registrando no serviceContainer
         */
        $container->add('routing', $map);
        $container->add('routing.matcher', $matcher);
        $container->add('routing.generator', $generator);
        $container->add(RequestInterface::class, $request);
        $container->addLazy(
            'route', function (ContainerInterface $container) {
            $matcher = $container->get('routing.matcher');
            $request = $container->get(RequestInterface::class);
            return $matcher->match($request);

        }
        );

    }

    /**
     * cria uma request com todas informações embutidas no objeto
     */
    protected function getRequest(): RequestInterface
    {
        /**
         * recebe todas variaveis globais do php
         */
        return ServerRequestFactory::fromGlobals(
            $_SERVER, $_GET, $_POST, $_COOKIE, $_FILES
        );
    }
}
