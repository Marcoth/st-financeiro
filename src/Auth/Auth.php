<?php
/**
 * Created by PhpStorm.
 * User: Marcos
 * Date: 25/07/2017
 * Time: 22:30
 */

namespace STFin\Auth;

use STFin\Models\UserInterface;

/**
 * classe associada a interface para implementação
 */
class Auth implements AuthInterface
{

    /**
     * @var JasnyAuth - instancia do jasnyAuth - classe de que intermedia a autenticacao
     */
    private $jasnyAuth;

    function __construct(JasnyAuth $jasnyAuth)
    {
        $this->jasnyAuth = $jasnyAuth;
        /**
         * garanti que inicie a sessão toda chamada de autenticacao
         */
        $this->sessionStart();
    }

    /**
     * metodo que realiza o login
     */
    public function login(array $credentials): bool
    {
        /**
         * passando email e password para o jasny
         */
        list('email' => $email, 'password' => $password) = $credentials;
        /**
         * retorna o usuario ou null
         */
        return $this->jasnyAuth->login($email, $password) !== null;
    }

    /**
     * verificao na sessão se o usuário é valido
     */
    public function check(): bool
    {
        return $this->user() !== null;
    }

    /**
     * logout do usuário
     */
    public function logout(): void
    {
        $this->jasnyAuth->logout();
    }

    /**
     * retorna um USerInterface ? - significa que pode ser null o retorno desse metodo
     */
    public function user(): ?UserInterface
    {
        return $this->jasnyAuth->user();
    }

    /**
     * criptografa a senha do usuario
     */
    public function hashPassword(string $password): string
    {
        return $this->jasnyAuth->hashPassword($password);
    }

    /**
     * Inicia sessao do usuário
     */
    protected function sessionStart()
    {
        /**
         * se a sessão não foi iniciada
         */
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }


}

