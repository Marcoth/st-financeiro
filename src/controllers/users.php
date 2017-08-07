<?php

use Psr\Http\Message\ServerRequestInterface;


/**
 * CRUD de Categoria
 */
$app
    ->get(
        '/users', function () use ($app) {
        $view = $app->service('view.renderer');
        $repository = $app->service('user.repository');
        $users = $repository->all();
        return $view->render(
            'users/list.html.twig', [
                'users' => $users
            ]
        );
    }, 'users.list'
    )
    ->get(
        '/users/new', function () use ($app) {
        $view = $app->service('view.renderer');
        return $view->render('users/create.html.twig');
    }, 'users.new'
    )
    ->post(
        '/users/store', function (ServerRequestInterface $request) use ($app) {
        /** capturando os dados da requisição */
        $data = $request->getParsedBody();
        $repository = $app->service('user.repository');
        $auth = $app->service('auth');
        $data['password'] = $auth->hashPassword($data['password']);
        $repository->create($data);
        /** redireciona para lista de categorias de custos */
        return $app->route('users.list');
    }, 'users.store'
    )
    ->get(
        '/users/{id}/edit', function (ServerRequestInterface $request) use ($app) {
        $view = $app->service('view.renderer');
        $id = $request->getAttribute('id');
        $repository = $app->service('user.repository');
        $users = $repository->find($id);
        return $view->render(
            'users/edit.html.twig', [
                'user' => $users
            ]
        );
    }, 'users.edit'
    )
    ->post(
        '/users/{id}/update', function (ServerRequestInterface $request) use ($app) {
        $repository = $app->service('user.repository');
        $id = $request->getAttribute('id');
        $user = $repository->find($id);
        $data = $request->getParsedBody();
        $repository->update($id, $data);
        return $app->route('users.list');
    }, 'users.update'
    )
    ->get(
        '/users/{id}/show', function (ServerRequestInterface $request) use ($app) {
        $view = $app->service('view.renderer');
        $repository = $app->service('user.repository');
        $id = $request->getAttribute('id');
        $user = $repository->find($id);
        return $view->render(
            'users/show.html.twig', [
                'user' => $user
            ]
        );
    }, 'users.show'
    )
    ->get(
        '/users/{id}/delete', function (ServerRequestInterface $request) use ($app) {
        $id = $request->getAttribute('id');
        $repository = $app->service('user.repository');
        $repository->delete($id);
        return $app->route('users.list');
    }, 'users.delete'
    );
