<?php

declare(strict_types=1);

namespace STFin\Plugins;

use Interop\Container\ContainerInterface;
use STFin\Auth\Auth;
use STFin\Auth\JasnyAuth;
use STFin\ServiceContainerInterface;

class AuthPlugin implements PluginInterface
{
    /**
     * registra  as configurações de credencias do banco de dados com o eloquente
     */
    public function register(ServiceContainerInterface $container)
    {
        /**
         * registrando o jasny auth
         */
        $container->addLazy(
            'jasny.auth', function (ContainerInterface $container) {
            /**
             * passando o repositorio de usuario para o construtor do jasny
             */
            return new JasnyAuth($container->get('user.repository'));
        }
        );
        /**
         * usando biblioteca de terceiro para implementar a autenticacao
         */
        $container->addLazy(
            'auth', function (ContainerInterface $container) {
            return new Auth($container->get('jasny.auth'));
        }
        );
    }
}
