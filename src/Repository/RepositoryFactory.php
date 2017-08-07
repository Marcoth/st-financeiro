<?php
/**
 * Created by PhpStorm.
 * User: Marcos
 * Date: 25/07/2017
 * Time: 19:45
 */
declare(strict_types=1);

namespace STFin\Repository;

/**
 * responsavel por criar os repositorys
 */
class RepositoryFactory
{


    public static function factory(string $modelClass)
    {

        return new DefaultRepository($modelClass);
    }
}
