<?php
/**
 * habilita o uso de tipagem nas variaveis
 */
declare(strict_types=1);

namespace STFin;


interface ServiceContainerInterface
{
    /**
     * adicionar serviço
     * @param  string $name
     * @param  $service
     * @return
     */
    public function add(string $name, $service);

    /**
     * adicionar serviço usando a estrategia lazy
     * @param string $name
     * @param callable $callable - função que contem a logica de chamada desse servico
     */
    public function addLazy(string $name, callable $callable);

    /**
     *  pega um serviço
     * @param string $name
     */
    public function get(string $name);

    /**
     * verifica se tem um serviço
     * @param  string $name
     * @return
     */
    public function has(string $name);
}
