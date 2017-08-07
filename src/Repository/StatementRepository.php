<?php
/**
 * Created by PhpStorm.
 * User: Marcos
 * Date: 25/07/2017
 * Time: 19:31
 */
declare(strict_types=1);

namespace STFin\Repository;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use STFin\Models\BillPay;
use STFin\Models\BillReceive;

/**
 * generic Repository - cumpri o contrato com a interface de extrato
 */
class StatementRepository implements StatementRepositoryInterface
{

    /**
     * consultar extrato
     */
    public function all(string $dateStart, string $dateEnd, int $user_id): array
    {
        /**
         * guarda todas as contas a pagar no periodo
         */
        $billPays = BillPay::query()
            ->selectRaw('bill_pays.*,category_costs.name as category_name')
            ->leftJoin('category_costs', 'category_costs.id', '=', 'bill_pays.category_cost_id')
            ->whereBetween('date_launch', [$dateStart, $dateEnd])
            ->where('bill_pays.user_id', $user_id)
            ->get();
        /**
         * guarda todas as contas a receber no periodo
         */
        $billReceives = BillReceive::query()
            ->whereBetween('date_launch', [$dateStart, $dateEnd])
            ->where('user_id', $user_id)
            ->get();
        /**
         *
         * @var $collection - criando uma nova coleção com o merge das contas a pagar e receber.
         */
        $collection = new Collection(array_merge_recursive($billPays->toArray(), $billReceives->toArray()));
        /**
         * @var $statements - gerando o extrato ordenado pela data
         */
        $statements = $collection->sortByDesc('date_launch');

        return [
            'statements' => $statements,
            'total_pays' => $billPays->sum('value'),
            'total_receives' => $billReceives->sum('value')
        ];

    }
}
