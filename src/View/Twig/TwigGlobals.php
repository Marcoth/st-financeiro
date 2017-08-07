<?php
/**
 * Created by PhpStorm.
 * User: Marcos
 * Date: 26/07/2017
 * Time: 00:17
 */

namespace STFin\View\Twig;

use STFin\Auth\AuthInterface;

/**
 * Classe que configura as variaveis globais que podem ser passadas para o template
 */
class TwigGlobals extends \Twig_Extension implements \Twig_Extension_GlobalsInterface
{


    /**
     * @var AuthInterface
     */
    private $auth;

    function __construct(AuthInterface $auth)
    {
        $this->auth = $auth;
    }

    /**
     * o twig cria variaveis globais disponiveis para o template
     */
    public function getGlobals()
    {
        return [
            'Auth' => $this->auth
        ];
    }

}
