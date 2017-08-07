<?php
/**
 * Created by PhpStorm.
 * User: Marcos
 * Date: 25/07/2017
 * Time: 19:27
 */
declare(strict_types=1);

namespace STFin\Repository;


interface RepositoryInteface
{

    /**
     * consulta registros
     */
    public function all(): array;

    /**
     * criar registro
     */
    public function create(array $data);

    /**
     * criar multiplos registros
     */
    public function manyCreate(array $data);

    /**
     * atualizar registro
     */
    public function update($id, array $data);

    /**
     * remove um registro
     */
    public function delete($id);

    /**
     * @param $failIfNotExist - falha quando o registro não existir
     */
    public function find(int $id, bool $failIfNotExist = true);

    /**
     * busca por um campo e retorna um array
     */
    public function findByField(string $field, $value);

    /**
     * consulta um registro baseado no id e user_id
     */
    public function findOneBy(array $search);
}
