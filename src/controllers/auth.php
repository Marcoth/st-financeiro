<?php

/**
 * CRUD de Categoria
 */

use Psr\Http\Message\ServerRequestInterface;

/**
 * login - get - ação para mostrar o formulário
 * login - post- ação para fazer o login
 * logout - get - ação de logout do usuário
 */
$app
    ->get(
        '/login', function () use ($app) {
        $view = $app->service('view.renderer');
        return $view->render('auth/login.html.twig');
    }, 'auth.show_login_form'
    )
    ->post(
        '/login', function (ServerRequestInterface $request) use ($app) {
        $view = $app->service('view.renderer');
        $auth = $app->service('auth');
        $data = $request->getParsedBody();
        /** @var $result - retorna true ou false da autenticacao do usuario */
        $result = $auth->login($data);
        if (!$result) {
            return $view->render('auth/login.html.twig', ['result' => $result]);
        }
        return $app->route('category-costs.list');
    }, 'auth.login'
    )
    ->get(
        '/logout', function () use ($app) {
        $app->service('auth')->logout();
        return $app->route('auth.show_login_form');
    }, 'auth.logout'
    );

/** barreira para area administrativa */
$app->before(
    function () use ($app) {
        $route = $app->service('route');
        $auth = $app->service('auth');
        /** definindo rotas que o usuario pode acessar sem autenticação */
        $routerWhiteList = [
            'auth.show_login_form',
            'auth.login'
        ];
        /** se a rota não estiver na lista  e o usuario não estiver logado */
        if (!in_array($route->name, $routerWhiteList) && !$auth->check()) {
            return $app->route('auth.show_login_form');
        }
    }
);
