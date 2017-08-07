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
 * Model para Lançamento de descontos
 */
class DiscountLaunch extends Model
{
    //Mass Assigment -
    protected $fillable = [
        'date_discount_launch',
        'type',
        'value',
        'bill_pay_id'
    ];

    /**
     * criando relacionamento com contas a pagar para recuperar o nome da conta
     */
    public function billPay()
    {
        /**
         *  uma conta a pagar pode ter varios lançamentos de desconto
         */
        return $this->belongsTo(BillPay::class);
    }
}
