<?php
declare(strict_types = 1);
/**
 * Created by PhpStorm.
 * User: Marcos
 * Date: 21/07/2017
 * Time: 19:25
 */

namespace STFin;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use STFin\Plugins\PluginInterface;
use Zend\Diactoros\Response;
use Zend\Diactoros\Response\RedirectResponse;

class Application
{
    /**
     * receber uma instancia do service container()
     * @var $serviceContainer
     */
    private $serviceContainer;
    /**
     * @var $befores
     */
    private $befores = [];

    /**
     * Application constructor.
     * @param $serviceContainer
     */
    public function __construct(ServiceContainerInterface $serviceContainer)
    {
        $this->serviceContainer = $serviceContainer;
    }

    public function service($name)
    {
        return $this->serviceContainer->get($name);
    }

    public function addService(string $name, $service): void
    {
        if (is_callable($service)) {
            $this->serviceContainer->addLazy($name, $service);
        } else {
            $this->serviceContainer->add($name, $service);
        }
    }

    /**
     * faz a integração de application para registro de novos
     * comportamentos do sistema
     * sem modelar a classe
     * */
    public function plugin(PluginInterface $plugin): void
    {
        $plugin->register($this->serviceContainer);
    }

    /**
     * acessa o servico de rotas para criação de rotas do tipo get
     * @param  $path -caminho da rota
     * @param  $action - ação quando a rota for acessada
     * @param  $name -  nomeando a rota para ser acessada
     * @return Application
     */
    public function get($path, $action, $name = null): Application
    {
        /**
         * @var $routing - plugando o serviço
         */
        $routing = $this->service('routing');
        $routing->get($name, $path, $action);
        return $this;
    }

    /**
     * acessa o servico de rotas para criação de rotas do tipo post
     *
     * @param  $path -caminho da rota
     * @param  $action - ação quando a rota for acessada
     * @param  $name -  nomeando a rota para ser acessada
     * @return Application
     */
    public function post($path, $action, $name = null): Application
    {
        /**
         * @var $routing - plugando o serviço
         */
        $routing = $this->service('routing');
        $routing->post($name, $path, $action);
        return $this;
    }

    /**
     * redirecionamento
     */
    public function redirect($path): ResponseInterface
    {
        return new RedirectResponse($path);
    }

    /**
     * acessa o redirect passando o path da view a ser redirecionada
     */
    public function route(string $name, array $params = []): ResponseInterface
    {
        $generator = $this->service('routing.generator');
        $path = $generator->generate($name, $params);
        return $this->redirect($path);

    }

    /**
     * recebe a função que vai ser armazenada na coleção de funções
     */
    public function before(callable $callback): Application
    {
        array_push($this->befores, $callback);
        return $this;
    }

    /**
     * varri a coleção de funções do before e as executas
     */
    protected function runBefores(): ?ResponseInterface
    {
        foreach ($this->befores as $callback) {
            /**
             * Invocando a função, guarda o resultado
             * Passa como parametro umm RequestInterface
             * que contém os dados da requisição para processar;
             */
            $result = $callback($this->service(RequestInterface::class));
            /**
             * se é um responseInterface, significa que
             * o processo foi travado em algum momento e isso caracteriza um erro.
             */
            if ($result instanceof ResponseInterface) {
                return $result;
            }
        }
        /**
         * não precisou travar a aplicação e retorna null
         */
        return null;
    }

    /**  */
    public function start(): void
    {
        /**
         * @var $route - pega a rota acessada pelo o usuário
         */
        $route = $this->service('route');
        /**
         * @var ServerRequestInterface $request - captura a request
         */
        $request = $this->service(RequestInterface::class);
        if (!$route) {
            echo 'Page not found';
            exit;
        }
        /**
         * acessando os atributos da rota
         */
        foreach ($route->attributes as $key => $value) {
            $request = $request->withAttribute($key, $value);
        }

        $result = $this->runBefores();
        if ($result) {
            $this->emitiResponse($result);
            return;
        }
        /**
         * @var $callable - pega ação
         */
        $callable = $route->handler;
        /**
         * executa a função que está dentro da variavel
         */
        $response = $callable($request);
        $this->emitiResponse($response);
    }

    /**
     * emissor de resposta http
     */
    protected function emitiResponse(ResponseInterface $response): void
    {
        /**
         * @var $emitter - Server API emissor baseador na API do diactoros
         */
        $emitter = new Response\SapiEmitter();
        $emitter->emit($response);
    }

}

