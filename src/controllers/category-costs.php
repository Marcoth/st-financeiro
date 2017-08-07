<?php

use Psr\Http\Message\ServerRequestInterface;


/**
 * CRUD de Categoria
 */
$app
    ->get(
        '/category-costs', function () use ($app) {
        $view = $app->service('view.renderer');
        $repository = $app->service('category-cost.repository');
        $auth = $app->service('auth');
        $categories = $repository->findByField('user_id', $auth->user()->getId());
        return $view->render(
            'category-costs/list.html.twig', [
                'categories' => $categories
            ]
        );
    }, 'category-costs.list'
    )
    ->get(
        '/category-costs/new', function () use ($app) {
        $view = $app->service('view.renderer');
        return $view->render('category-costs/create.html.twig');
    }, 'category-costs.new'
    )
    ->post(
        '/category-costs/store', function (ServerRequestInterface $request) use ($app) {
        /** capturando os dados da requisição*/
        $data = $request->getParsedBody();
        $repository = $app->service('category-cost.repository');
        $auth = $app->service('auth');
        $data['user_id'] = $auth->user()->getId();
        $repository->create($data);
        /** redireciona para lista de categorias de custos */
        return $app->route('category-costs.list');
    }, 'category-costs.store'
    )
    ->get(
        '/category-costs/{id}/edit', function (ServerRequestInterface $request) use ($app) {
        $view = $app->service('view.renderer');
        $id = $request->getAttribute('id');
        $repository = $app->service('category-cost.repository');
        $auth = $app->service('auth');
        $category = $repository->findOneBy(
            [
                'id' => $id,
                'user_id' => $auth->user()->getId()
            ]
        );
        return $view->render(
            'category-costs/edit.html.twig', [
                'category' => $category
            ]
        );
    }, 'category-costs.edit'
    )
    ->post(
        '/category-costs/{id}/update', function (ServerRequestInterface $request) use ($app) {
        $repository = $app->service('category-cost.repository');
        $id = $request->getAttribute('id');
        $data = $request->getParsedBody();
        $auth = $app->service('auth');
        $data['user_id'] = $auth->user()->getId();
        $repository->update(
            [
                'id' => $id,
                'user_id' => $auth->user()->getId()
            ], $data
        );
        return $app->route('category-costs.list');
    }, 'category-costs.update'
    )
    ->get(
        '/category-costs/{id}/show', function (ServerRequestInterface $request) use ($app) {
        $view = $app->service('view.renderer');
        $repository = $app->service('category-cost.repository');
        $id = $request->getAttribute('id');
        $auth = $app->service('auth');
        $category = $repository->findOneBy(
            [
                'id' => $id,
                'user_id' => $auth->user()->getId()
            ]
        );
        return $view->render(
            'category-costs/show.html.twig', [
                'category' => $category
            ]
        );
    }, 'category-costs.show'
    )
    ->get(
        '/category-costs/{id}/delete', function (ServerRequestInterface $request) use ($app) {
        $id = $request->getAttribute('id');
        $auth = $app->service('auth');
        $repository = $app->service('category-cost.repository');
        $repository->delete(
            [
                'id' => $id,
                'user_id' => $auth->user()->getId()
            ]
        );
        return $app->route('category-costs.list');
    }, 'category-costs.delete'
    );
