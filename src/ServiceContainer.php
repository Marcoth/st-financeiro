<?php
/**
 * Created by PhpStorm.
 * User: Marcos
 * Date: 21/07/2017
 * Time: 19:11
 */

namespace STFin;


use Xtreamwayz\Pimple\Container;

class ServiceContainer implements ServiceContainerInterface
{
    /**
     * @var $container - instancia do container do pimple
     */
    private $container;

    function __construct()
    {
        $this->container = new Container();
    }


    /**
     * adicionar serviço
     * @param string $name
     * @param $service
     */
    public function add(string $name, $service)
    {
        //armazenando o serviço no container
        $this->container[$name] = $service;
    }

    /**
     * adicionar serviço usando a estrategia lazy
     * @param string $name
     * @param callable $callable - função que contem a logica de chamada desse servico
     */
    public function addLazy(string $name, callable $callable)
    {
        //vai produzir o serviço de acordo com a função passada, guardando no container
        $this->container[$name] = $this->container->factory($callable);
    }

    /**
     *  pega um serviço
     * @param  string $name - nome do servico
     * @return mixed
     */
    public function get(string $name)
    {
        //retorna o serviço
        return $this->container->get($name);
    }

    /**
     * verifica se tem um serviço
     * @param string $name - nome do servico
     */
    public function has(string $name)
    {
        //verifica se o serviço existe , retorna um booleano
        $this->container->has($name);

    }
}
