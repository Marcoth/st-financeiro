<?php
/**
 * Created by PhpStorm.
 * User: Marcos
 * Date: 22/07/2017
 * Time: 00:54
 */

namespace STFin\View;


use Psr\Http\Message\ResponseInterface;
use Zend\Diactoros\Response;

class ViewRenderer implements ViewRendererInterface
{
    /**
     * @var \Twig_Environment
     */
    private $twigEnviroment;

    /**
     * Passa a instancia $twigEnviroment via injeção de dependencia
     */
    public function __construct(\Twig_Environment $twigEnviroment)
    {
        $this->twigEnviroment = $twigEnviroment;
    }


    /**
     * @param $template - qual template sera renderizado
     * @param $context - variaveis , informações que vão ser renderizadas no template;
     * */

    /**
     * @param string $template - qual template sera renderizado
     * @param array $context - variaveis , informações que vão ser renderizadas
     *                         no
     * template;
     * @return ResponseInterface|ResponseInterfacenterface
     */
    public function render(string $template, array $context = []): ResponseInterface
    {
        // TODO: Implement render() method.
        $result = $this->twigEnviroment->render($template, $context);
        /**
         *
         * gerando um responseInteraface
         */
        $response = new Response();
        $response->getBody()->write($result);
        return $response;
    }

}
