<?php

declare(strict_types=1);

namespace STFin\Repository;

/**
 * Interface para grafico
 */
interface CategoryCostRepositoryInterface extends RepositoryInteface
{
    public function sumByPeriod(string $dateStart,string $dateEnd,int $userId) : array;
}
