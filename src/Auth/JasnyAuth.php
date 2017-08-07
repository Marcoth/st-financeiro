<?php
/**
 * Created by PhpStorm.
 * User: Marcos
 * Date: 25/07/2017
 * Time: 22:43
 */

namespace STFin\Auth;

use Jasny\Auth\Sessions;
use Jasny\Auth\User;
use STFin\Repository\RepositoryInteface;

/**
 * classe que servirá como um serviço de autenticação para o jasny
 * para fazer consulta no banco e informar qual o campo que queremos que seja autenticado
 */
class JasnyAuth extends \Jasny\Auth
{

    /**
     * treat que se encarrega de implementar os outros dois métodos da interface \Jasny\Auth
     */
    use Sessions;

    /**
     * @var RepositoryInteface - quando instaciarmos JasnyAuth ,
     * devemos informar o repository para que possamos utlizar dentro dela;
     */
    private $repository;

    function __construct(RepositoryInteface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Fetch a user by ID
     * @param  int|string $id
     * @return User|null
     */
    public function fetchUserById($id)
    {
        /**
         *
         * false - pra não receber excessão quando o User for null
         */
        return $this->repository->find($id, false);
    }

    /**
     * Fetch a user by username - email
     * @param  string $username
     * @return User|null
     */
    public function fetchUserByUsername($username)
    {
        $result = $this->repository->findByField('email', $username);
        return count($result) ? $result[0] : null;
    }
}
