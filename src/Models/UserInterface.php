<?php

namespace STFin\Models;

/**
 * 
 * interface  de manipulação do usuário 
 */
interface UserInterface
{

    public function getId():int;
    public function getFullName():string;
    public function getEmail():string;
    public function getPassword():string;

}
