<?php
/**
 * Created by PhpStorm.
 * User: Marcos
 * Date: 21/07/2017
 * Time: 20:38
 */

namespace STFin\Plugins;


use STFin\ServiceContainerInterface;

interface PluginInterface
{
    public function register(ServiceContainerInterface $container);
}