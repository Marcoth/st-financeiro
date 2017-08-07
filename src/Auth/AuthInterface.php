<?php
/**
 * Created by PhpStorm.
 * User: Marcos
 * Date: 25/07/2017
 * Time: 22:26
 */

declare(strict_types=1);

namespace STFin\Auth;

use STFin\Models\UserInterface;

/**
 * Interface que vai delimitar os metodos necessários  para autentiçação
 */
interface AuthInterface
{

    /**
     * metodo que realiza o login
     */
    public function login(array $credentials): bool;

    /**
     * verificao na sessão se o usuário é valido
     */
    public function check(): bool;

    /**
     * logout do usuário
     */
    public function logout(): void;

    /**
     * criptografa a senha do usuario
     */
    public function hashPassword(string $password): string;

    /**
     * retorna um USerInterface ? - significa que pode ser null o retorno desse metodo
     */
    public function user(): ?UserInterface;
}
