<?php

declare(strict_types=1);

namespace STFin\Plugins;

use Interop\Container\ContainerInterface;
use STFin\ServiceContainerInterface;
use STFin\View\Twig\TwigGlobals;
use STFin\View\ViewRenderer;

class ViewPlugin implements PluginInterface
{
    /**
     * registra o serviço do twig para renderizar as paginas
     */
    public function register(ServiceContainerInterface $container)
    {

        $container->addLazy(
            'twig', function (ContainerInterface $container) {
            /**
             * @var $loader - carrega o nossos templates
             */
            $loader = new \Twig_Loader_Filesystem(__DIR__ . '/../../templates');
            $twig = new \Twig_Environment($loader);
            /**
             * @var $auth - recupera o serviço de autenticacao
             */
            $auth = $container->get('auth');

            /**
             * acessando o generator para pegar a rota e o nome dela
             */
            $generator = $container->get('routing.generator');
            /**
             * adiciona variabel de autenticacao de forma
             * global no twig para ser acessa em todo template
             */
            $twig->addExtension(new TwigGlobals($auth));
            /**
             * add uma funcção no twig para expor na view
             */
            $twig->addFunction(
                new \Twig_SimpleFunction(
                    'route', function (string $name, array $params = []) use ($generator) {
                    return $generator->generate($name, $params);
                }
                )
            );
            return $twig;
        }
        );

        $container->addLazy(
            'view.renderer', function (ContainerInterface $container) {
            $twigEnviroment = $container->get('twig');
            return new ViewRenderer($twigEnviroment);
        }
        );
    }
}
