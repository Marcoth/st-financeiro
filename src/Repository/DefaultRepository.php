<?php
/**
 * Created by PhpStorm.
 * User: Marcos
 * Date: 25/07/2017
 * Time: 19:31
 */
declare(strict_types=1);

namespace STFin\Repository;

use Illuminate\Database\Eloquent\Model;
use STFin\Models\BillPay;
use STFin\Models\BillReceive;
use STFin\Models\DiscountLaunch;
use STFin\Repository;

/**
 * generic Repository - cumpri o contrato com a interface
 */
class DefaultRepository implements RepositoryInteface
{
    /**
     * @var string $modelClass
     */
    private $modelClass;
    /**
     * @var $model - Model
     */
    private $model;

    function __construct(string $modelClass)
    {
        /**
         * @var modelClass - string com o nome da classe
         */
        $this->modelClass = $modelClass;
        /**
         * @var model - realiza a instancia da classe com o modelClass(nome da classe)
         */
        $this->model = new $modelClass;
    }

    /**
     * consulta registros
     */
    public function all(): array
    {
        return $this->model->all()->toArray();
    }

    /**
     * criar registro
     */
    public function create(array $data)
    {
        /** remove campos não obrigatórios para inserção */
        unset($data['date_ini']);
        unset($data['date_end']);
        $this->model->fill($data);
        $this->model->save();
        return $this->model;
    }

    /** criando varios novos registros  */
    public function manyCreate(array $data)
    {
        $this->model->create($data);
        return $this->model;
    }

    /**
     * atualizar registro
     */
    public function update($id, array $data)
    {
        $model = $this->findInternal($id);
        $model->fill($data);
        $model->save();
        return $model;
    }

    /**
     * remove um registro
     */
    public function delete($id)
    {
        $model = $this->findInternal($id);
        $model->delete();
    }

    public function find(int $id, bool $failIfNotExist = true)
    {
        /**
         * se permitir falha , lança excessão , senão usa apenas find que não lança excessao
         * e não vai matar a aplicação
         */
        return $failIfNotExist ? $this->model->findOrFail($id) :
            $this->model->find($id);
    }

    /**
     * busca por um campo e retorna um array
     */
    public function findByField(string $field, $value, $dateStart = null, $dateEnd = null, $table = null)
    {
        if ($dateStart != null && $dateEnd != null) {
            if ($table == 'bill_receives') {
                $bills = BillReceive::query()
                    ->whereBetween('date_launch', [$dateStart, $dateEnd])
                    ->where('user_id', $value)
                    ->get();
            }
            if ($table == 'bill_pays') {
                $bills = BillPay::query()
                    ->selectRaw('bill_pays.*,category_costs.name as category_name')
                    ->leftJoin('category_costs', 'category_costs.id', '=', 'bill_pays.category_cost_id')
                    ->whereBetween('date_launch', [$dateStart, $dateEnd])
                    ->where('bill_pays.user_id', $value)
                    ->get();
            }
            if ($table == 'discount_launches') {
                $bills = DiscountLaunch::query()
                    ->selectRaw('discount_launches.*,bill_pays.name as bill_pay_name')
                    ->leftJoin('bill_pays', 'bill_pays.id', 'discount_launches.bill_pay_id')
                    ->whereBetween('date_discount_launch', [$dateStart, $dateEnd])
                    ->where('bill_pays.user_id', $value)
                    ->get();
            }
            return $bills;

        } else {
            return $this->model->where($field, '=', $value)->get();
        }
    }

    /**
     *
     * consulta um registro baseado no id e user_id
     */
    public function findOneBy(array $search)
    {
        /**
         * criador da query
         */
        $queryBuilder = $this->model;
        foreach ($search as $field => $value) {
            /**
             * a cada where guarda um novo valor no queryBuilder
             */
            $queryBuilder = $queryBuilder->where($field, '=', $value);
        }
        /**
         * analisa se for algo retornado pela consulta,se não ele falha
         */
        return $queryBuilder->firstOrFail();
    }

    protected function findInternal($id)
    {
        return is_array($id) ? $model = $this->findOneBy($id) : $this->find((int)$id);
    }
}
