<?php
/**
 * Created by PhpStorm.
 * User: Marcos
 * Date: 24/07/2017
 * Time: 21:23
 */

namespace STFin\Models;


use Illuminate\Database\Eloquent\Model;

class CategoryCost extends Model
{
    //Mass Assigment
    protected $fillable = [
        'name',
        'user_id'
    ];
}
