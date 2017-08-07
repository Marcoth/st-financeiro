<?php

/**
 * CRUD de Contas a pagar
 */

use Psr\Http\Message\ServerRequestInterface;


$app
    ->get(
        '/discount-launch', function () use ($app) {
        $view = $app->service('view.renderer');
        /** @var $repository - injetando repository no controller */
        $repository = $app->service('discount-launch.repository');
        $auth = $app->service('auth');
        /** lista lançamentos de descontos dos proximos 30 dias */
        $dateStart = (new \DateTime())->modify('-1 days');
        $dateStart = $dateStart instanceof \DateTime ? $dateStart->format('Y-m-d')
            : \DateTime::createFromFormat('d/m/Y', $dateStart)->format('Y-m-d');
        $dateEnd = (new \DateTime())->modify('+30 days ');
        $dateEnd = $dateEnd instanceof \DateTime ? $dateEnd->format('Y-m-d')
            : \DateTime::createFromFormat('d/m/Y', $dateEnd)->format('Y-m-d');
        //lista de lançamentos de descontos - retorna os lançamentos vinculados as contas  a pagar do usuário logado
        $discounts = $repository->findByField('user_id', $auth->user()->getId(), $dateStart, $dateEnd, 'discount_launches');
        return $view->render(
            'discount-launch/list.html.twig', [
                'discounts' => $discounts
            ]
        );
    }, 'discount-launch.list')
    ->get(
        '/discount-launch/new', function () use ($app) {
        $view = $app->service('view.renderer');
        $auth = $app->service('auth');
        /** @var $billPayRepository - consultando contas a pagar acessando repository
         * de contas a pagar
         */
        $billPayRepository = $app->service('bill-pay.repository');
        $billPays = $billPayRepository->findByField('user_id', $auth->user()->getId());
        return $view->render(
            'discount-launch/create.html.twig', [
                'billPays' => $billPays
            ]
        );
    }, 'discount-launch.new')
    ->post(
        '/discount-launch/store', function (ServerRequestInterface $request) use ($app) {
        /** capturando os dados da requisição */
        $data = $request->getParsedBody();
        $repository = $app->service('discount-launch.repository');
        $billPayRepository = $app->service('bill-pay.repository');
        $auth = $app->service('auth');
        $data['date_discount_launch'] = dateParse($data['date_discount_launch']);
        /**  validando se a conta pertence a esse usuário e retorno id da conta */
        $data['bill_pay_id'] = $billPayRepository->findOneBy(
            [
                'id' => $data['bill_pay_id'],
                'user_id' => $auth->user()->getId()
            ]
        )->id;
        /** salvar lançamento */
        $repository->create($data);
        $dataPay = [];
        $dataPay['value'] = $data['value_with_discount'];
        /** atualizar o valor na conta   */
        $billPayRepository->update(
            [
                'id' => $data['bill_pay_id'],
                'user_id' => $auth->user()->getId()
            ], $dataPay
        );
        /** redireciona para lista de lançamentos de desconto */
        return $app->route('discount-launch.list');
    }, 'discount-launch.store')
    ->get(
        '/discount-launch/{id}/edit', function (ServerRequestInterface $request) use ($app) {
        $view = $app->service('view.renderer');
        $id = $request->getAttribute('id');
        $repository = $app->service('discount-launch.repository');
        $auth = $app->service('auth');
        $discount = $repository->findOneBy(
            [
                'id' => $id
            ]
        );
        /** @var $categoryRepository - consultando categorias acessando
         * repository de categoria de custos
         */
        $categoryRepository = $app->service('bill-pay.repository');
        $billPays = $categoryRepository->findByField('user_id', $auth->user()->getId());
        return $view->render(
            'discount-launch/edit.html.twig', [
                'discount' => $discount,
                'billPays' => $billPays
            ]
        );
    }, 'discount-launch.edit'
    )
    ->post(
        '/discount-launch/{id}/update', function (ServerRequestInterface $request) use ($app) {
        $repository = $app->service('discount-launch.repository');
        $billPayRepository = $app->service('bill-pay.repository');
        $id = $request->getAttribute('id');
        $data = $request->getParsedBody();
        $auth = $app->service('auth');
        $data['user_id'] = $auth->user()->getId();
        $data['date_discount_launch'] = dateParse($data['date_discount_launch']);
        $data['bill_pay_id'] = $data['bill_pay_id_edit'];
        $data['bill_pay_id'] = $billPayRepository->findOneBy(
            [
                'id' => $data['bill_pay_id'],
                'user_id' => $auth->user()->getId()
            ]
        )->id;
        /** atualiza lançamento */
        $repository->update(
            [
                'id' => $id,
            ], $data
        );
        /** atualiza o valor na conta */
        $dataPay = [];
        $dataPay['value'] = $data['value_with_discount'] != "" ? $data['value_with_discount'] : "";
        /** atualizar o valor na conta   */
        if ($dataPay['value'] != '') {
            $billPayRepository->update(
                [
                    'id' => $data['bill_pay_id'],
                    'user_id' => $auth->user()->getId()
                ], $dataPay
            );
        }
        return $app->route('discount-launch.list');
    }, 'discount-launch.update'
    )
    ->get(
        '/discount-launch/{id}/show', function (ServerRequestInterface $request) use ($app) {
        $view = $app->service('view.renderer');
        $repository = $app->service('discount-launch.repository');
        $billPayRepository = $app->service('bill-pay.repository');
        $id = $request->getAttribute('id');
        $auth = $app->service('auth');
        $discount = $repository->findOneBy(
            [
                'id' => $id,
            ]
        );
        return $view->render(
            'discount-launch/show.html.twig', [
                'discount' => $discount
            ]
        );
    }, 'discount-launch.show'
    )
    ->get(
        '/discount-launch/{id}/delete', function (ServerRequestInterface $request) use ($app) {
        $id = $request->getAttribute('id');
        $auth = $app->service('auth');
        $repository = $app->service('discount-launch.repository');
        $billPayRepository = $app->service('bill-pay.repository');
        /** @var  $discount - recupera o id da conta que possui o  desconto */
        $discount = $repository->findOneBy(
            [
                'id' => $id
            ]
        );
        /** @var $currentValuePay - recuperar valor atual dessa conta */
        $currentValuePay = $billPayRepository->findOneBy(
            [
                'id' => $discount['bill_pay_id'],
                'user_id' => $auth->user()->getId()
            ]
        )->value;
        /** remove o desconto do valor da conta  */
        $newBillValue = $currentValuePay + $discount['value'];
        $dataPay['value'] = $newBillValue;
        /** atualiza o valor da conta com o desconto removido*/
        $billPayRepository->update(
            [
                'id' => $discount['bill_pay_id'],
                'user_id' => $auth->user()->getId()
            ], $dataPay
        );
        /** deleta o lançamento de desconto */
        $repository->delete(
            [
                'id' => $id,
            ]
        );
        return $app->route('discount-launch.list');
    }, 'discount-launch.delete'
    );
