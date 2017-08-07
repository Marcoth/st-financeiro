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
 * Model para Contas a receber
 */
class BillReceive extends Model
{
    //Mass Assigment -
    protected $fillable = [
        'date_launch',
        'name',
        'recurrent',
        'value',
        'user_id'
    ];
}
