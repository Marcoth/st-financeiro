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
use STFin\Models\CategoryCost;

/**
 * Category Repository - cumpri o contrato com a interface
 * extends de defaultRepository - pega todos os metodos no repositorio
 * implements CategoryCostRepository - implementa apenas sumByPeriod
 */
class CategoryCostRepository extends DefaultRepository implements CategoryCostRepositoryInterface
{

    /**
     * sobrescreve o construtor e chama o construtor da classe pai -DefaultRepository
     */
    function __construct()
    {
        parent::__construct(CategoryCost::class);

    }

    /**
     * Soma por Periodo
     */
    public function sumByPeriod(string $dateStart, string $dateEnd, int $userId): array
    {
        /**
         * @var $categories - utilizando queryBuilder
         * Soma por periodo agrupado por categoria /valor
         */
        $categories = CategoryCost::query()
            ->selectRaw('category_costs.name, sum(value) as value')
            ->leftJoin('bill_pays', 'bill_pays.category_cost_id', '=', 'category_costs.id')
            ->whereBetween('date_launch', [$dateStart, $dateEnd])
            ->where('category_costs.user_id', $userId)
            ->whereNotNull('bill_pays.category_cost_id')
            ->groupBy('value')
            ->groupBy('category_costs.name')
            ->get();
        return $categories->toArray();

    }
}
