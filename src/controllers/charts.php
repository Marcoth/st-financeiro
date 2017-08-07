<?php

use Psr\Http\Message\ServerRequestInterface;


/**
 * Lista o extrato
 */
$app
    ->get(
        '/charts', function (ServerRequestInterface $request) use ($app) {
        $view = $app->service('view.renderer');
        $repository = $app->service('category-cost.repository');
        $auth = $app->service('auth');
        /** @var $data - capturando parametros da url */
        $data = $request->getQueryParams();
        /** verificando se as datas estão corretas - obs usando nucolace */
        $dateStart = $data['date_start'] ?? (new \DateTime())->modify('-1 month');
        $dateStart = $dateStart instanceof \DateTime ? $dateStart->format('Y-m-d')
            : \DateTime::createFromFormat('d/m/Y', $dateStart)->format('Y-m-d');

        $dateEnd = $data['date_end'] ?? new \DateTime();
        $dateEnd = $dateEnd instanceof \DateTime ? $dateEnd->format('Y-m-d')
            : \DateTime::createFromFormat('d/m/Y', $dateEnd)->format('Y-m-d');

        /** chama repository e faz a busca */
        $categories = $repository->sumByPeriod($dateStart, $dateEnd, $auth->user()->getId());

        return $view->render(
            'charts.html.twig', [
                'categories' => $categories
            ]
        );
    }, 'charts.list'
    );
