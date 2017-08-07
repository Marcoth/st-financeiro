<?php

use Psr\Http\Message\ServerRequestInterface;


/**
 * CRUD de Categoria
 */
$app
    ->get(
        '/bill-receives', function () use ($app) {
        $view = $app->service('view.renderer');
        $repository = $app->service('bill-receive.repository');
        $auth = $app->service('auth');
        /**
         * lista contas a receber a vencer nos proximos 30 dias
         */
        $dateStart = (new \DateTime())->modify('-1 days');
        $dateStart = $dateStart instanceof \DateTime ? $dateStart->format('Y-m-d')
            : \DateTime::createFromFormat('d/m/Y', $dateStart)->format('Y-m-d');

        $dateEnd = (new \DateTime())->modify('+30 days ');
        $dateEnd = $dateEnd instanceof \DateTime ? $dateEnd->format('Y-m-d')
            : \DateTime::createFromFormat('d/m/Y', $dateEnd)->format('Y-m-d');
        $bills = $repository->findByField('user_id', $auth->user()->getId(), $dateStart, $dateEnd, 'bill_receives');
        return $view->render(
            'bill-receives/list.html.twig', [
                'bills' => $bills
            ]
        );
    }, 'bill-receives.list'
    )
    ->get(
        '/bill-receives/new', function () use ($app) {
        $view = $app->service('view.renderer');
        return $view->render('bill-receives/create.html.twig');
    }, 'bill-receives.new'
    )
    ->post(
        '/bill-receives/store', function (ServerRequestInterface $request) use ($app) {
        /**
         * capturando os dados da requisição
         */
        $data = $request->getParsedBody();
        $repository = $app->service('bill-receive.repository');
        $auth = $app->service('auth');
        $data['user_id'] = $auth->user()->getId();
        $data['date_launch'] = dateParse($data['date_launch']);
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
        /**
         * redireciona para lista de categorias de custos
         */
        return $app->route('bill-receives.list');
    }, 'bill-receives.store'
    )
    ->get(
        '/bill-receives/{id}/edit', function (ServerRequestInterface $request) use ($app) {
        $view = $app->service('view.renderer');
        $id = $request->getAttribute('id');
        $repository = $app->service('bill-receive.repository');
        $auth = $app->service('auth');
        $bill = $repository->findOneBy(
            [
                'id' => $id,
                'user_id' => $auth->user()->getId()
            ]
        );
        return $view->render(
            'bill-receives/edit.html.twig', [
                'bill' => $bill
            ]
        );
    }, 'bill-receives.edit'
    )
    ->post(
        '/bill-receives/{id}/update', function (ServerRequestInterface $request) use ($app) {
        $repository = $app->service('bill-receive.repository');
        $id = $request->getAttribute('id');
        $data = $request->getParsedBody();
        $auth = $app->service('auth');
        $data['user_id'] = $auth->user()->getId();
        $data['date_launch'] = dateParse($data['date_launch']);
        $repository->update(
            [
                'id' => $id,
                'user_id' => $auth->user()->getId()
            ], $data
        );
        return $app->route('bill-receives.list');
    }, 'bill-receives.update'
    )
    ->get(
        '/bill-receives/{id}/show', function (ServerRequestInterface $request) use ($app) {
        $view = $app->service('view.renderer');
        $repository = $app->service('bill-receive.repository');
        $id = $request->getAttribute('id');
        $auth = $app->service('auth');
        $bill = $repository->findOneBy(
            [
                'id' => $id,
                'user_id' => $auth->user()->getId()
            ]
        );
        return $view->render(
            'bill-receives/show.html.twig', [
                'bill' => $bill
            ]
        );
    }, 'bill-receives.show'
    )
    ->get(
        '/bill-receives/{id}/delete', function (ServerRequestInterface $request) use ($app) {
        $id = $request->getAttribute('id');
        $auth = $app->service('auth');
        $repository = $app->service('bill-receive.repository');
        $repository->delete(
            [
                'id' => $id,
                'user_id' => $auth->user()->getId()
            ]
        );
        return $app->route('bill-receives.list');
    }, 'bill-receives.delete'
    );
