<?php

declare(strict_types=1);

namespace STFin\Repository;

/**
 * Interface para Extrato
 */
interface StatementRepositoryInterface
{
    /**
     * consultar extrato
     */
    public function all(string $dateStart, string $dateEnd, int $user_id): array;
}
