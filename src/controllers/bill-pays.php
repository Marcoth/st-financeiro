<?php

/**
 * CRUD de Contas a pagar
 */

use Psr\Http\Message\ServerRequestInterface;


$app
    ->get(
        '/bill-pays', function () use ($app) {
        $view = $app->service('view.renderer');
        /** @var $repository - injetando repository no controller */
        $repository = $app->service('bill-pay.repository');
        $auth = $app->service('auth');
        /** lista contas a pagar a vencer nos proximos 30 dias */
        $dateStart = (new \DateTime('NOW'))->modify('-1 days');
        $dateStart = $dateStart instanceof \DateTime ? $dateStart->format('Y-m-d')
            : \DateTime::createFromFormat('d/m/Y', $dateStart)->format('Y-m-d');
        $dateEnd = (new \DateTime())->modify('+30 days ');
        $dateEnd = $dateEnd instanceof \DateTime ? $dateEnd->format('Y-m-d')
            : \DateTime::createFromFormat('d/m/Y', $dateEnd)->format('Y-m-d');
        $bills = $repository->findByField('user_id', $auth->user()->getId(), $dateStart, $dateEnd, 'bill_pays');
        return $view->render(
            'bill-pays/list.html.twig', [
                'bills' => $bills
            ]
        );
    }, 'bill-pays.list'
    )
    ->get(
        '/bill-pays/new', function () use ($app) {
        $view = $app->service('view.renderer');
        $auth = $app->service('auth');
        /** @var $categoryRepository - consultando categorias acessando repository
         * de categoria de custos
         */
        $categoryRepository = $app->service('category-cost.repository');
        $categories = $categoryRepository->findByField('user_id', $auth->user()->getId());
        return $view->render(
            'bill-pays/create.html.twig', [
                'categories' => $categories
            ]
        );
    }, 'bill-pays.new'
    )
    ->post(
        '/bill-pays/store', function (ServerRequestInterface $request) use ($app) {
        /** capturando os dados da requisição */
        $data = $request->getParsedBody();
        $repository = $app->service('bill-pay.repository');
        $categoryRepository = $app->service('category-cost.repository');
        $auth = $app->service('auth');
        $data['user_id'] = $auth->user()->getId();
        $data['date_launch'] = dateParse($data['date_launch']);
//            $data['value'] = numberParse($data['value']);
        $data['category_cost_id'] = $categoryRepository->findOneBy(
            [
                'id' => $data['category_cost_id'],
                'user_id' => $auth->user()->getId()
            ]
        )->id;
        $return = json_encode($repository->create($data));
        /** se a conta for recorrente  */
        if ($data['recurrent'] == '1' && $return != null) {
            /** extrai quantidade de meses definidos nas datas de inicio e fim*/
            $dateBegin = explode('-', $data['date_ini']);
            $dateEnd = explode('-', $data['date_end']);
            /** @var  $qtdeMonth - quantidade de meses */
            $qtdeMonth = (int)$dateEnd[1] - (int)$dateBegin[1];
            /** gerar lançamentos recorrentes */
            for ($i = 0; $i < $qtdeMonth; $i++) {
                /**  defini proximos meses */
                $meses = $dateBegin[1] + $i + 1;
                /** substitui meses no data_launch */
                $dateLaunch = explode('-', $data['date_launch']);
                $dateLaunch[1] = $meses < 10 ? '0' . $meses : $meses;
                $dateLaunchFormat = implode('-', $dateLaunch);
                $data['date_launch'] = $dateLaunchFormat;
                $data['date_launch'] = dateParse($data['date_launch']);
                $repository->manyCreate($data);
            }
        }
        /** redireciona para lista de categorias de custos */
        return $app->route('bill-pays.list');
    }, 'bill-pays.store'
    )
    ->get(
        '/bill-pays/{id}/edit', function (ServerRequestInterface $request) use ($app) {
        $view = $app->service('view.renderer');
        $id = $request->getAttribute('id');
        $repository = $app->service('bill-pay.repository');
        $auth = $app->service('auth');
        $bill = $repository->findOneBy(
            [
                'id' => $id,
                'user_id' => $auth->user()->getId()
            ]
        );
        /** @var $categoryRepository - consultando categorias acessando
         * repository de categoria de custos
         */
        $categoryRepository = $app->service('category-cost.repository');
        $categories = $categoryRepository->findByField('user_id', $auth->user()->getId());
        return $view->render(
            'bill-pays/edit.html.twig', [
                'bill' => $bill,
                'categories' => $categories
            ]
        );
    }, 'bill-pays.edit'
    )
    ->post(
        '/bill-pays/{id}/update', function (ServerRequestInterface $request) use ($app) {
        $repository = $app->service('bill-pay.repository');
        $categoryRepository = $app->service('category-cost.repository');
        $id = $request->getAttribute('id');
        $data = $request->getParsedBody();
        $auth = $app->service('auth');
        $data['user_id'] = $auth->user()->getId();
        $data['date_launch'] = dateParse($data['date_launch']);
        $data['category_cost_id'] = $categoryRepository->findOneBy(
            [
                'id' => $data['category_cost_id'],
                'user_id' => $auth->user()->getId()
            ]
        )->id;
        $repository->update(
            [
                'id' => $id,
                'user_id' => $auth->user()->getId()
            ], $data
        );
        return $app->route('bill-pays.list');
    }, 'bill-pays.update'
    )
    ->get(
        '/bill-pays/{id}/show', function (ServerRequestInterface $request) use ($app) {
        $view = $app->service('view.renderer');
        $repository = $app->service('bill-pay.repository');
        $id = $request->getAttribute('id');
        $auth = $app->service('auth');
        $bill = $repository->findOneBy(
            [
                'id' => $id,
                'user_id' => $auth->user()->getId()
            ]
        );
        return $view->render(
            'bill-pays/show.html.twig', [
                'bill' => $bill
            ]
        );
    }, 'bill-pays.show'
    )
    ->get(
        '/bill-pays/{id}/delete', function (ServerRequestInterface $request) use ($app) {
        $id = $request->getAttribute('id');
        $auth = $app->service('auth');
        $repository = $app->service('bill-pay.repository');
        $repository->delete(
            [
                'id' => $id,
                'user_id' => $auth->user()->getId()
            ]
        );
        return $app->route('bill-pays.list');
    }, 'bill-pays.delete'
    );
