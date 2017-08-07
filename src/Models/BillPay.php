<?php
/**
 * Created by PhpStorm.
 * User: Marcos
 * Date: 24/07/2017
 * Time: 21:23
 */

namespace STFin\Models;


use Illuminate\Database\Eloquent\Model;
/**
 * Model para Contas a pagar
 */
class BillPay extends Model
{
    //Mass Assigment -
    protected $fillable = [
        'date_launch',
        'name',
        'recurrent',
        'value',
        'user_id',
        'category_cost_id'
    ];

    /**
     * criando relacionamento com categoria de custo para recuperar o nome da categoria
     */
    public function categoryCost()
    {
        /**
         * Uma categoria pode está em varias contas a pagar.
         * e uma conta a pagar vai ter uma categoria
         */
        return $this->belongsTo(CategoryCost::class);
    }
}
