<?php

declare(strict_types=1);

namespace STFin\View;

use Psr\Http\Message\ResponseInterface;

/**
 * definindo comportamento de renderização o template
 */
interface ViewRendererInterface
{
    /**
     * @param string $template - qual template sera renderizado
     * @param array $context - variaveis , informações que vão ser renderizadas no template;
     * @return ResponseInterface|ResponseInterfacenterface
     */
    public function render(string $template, array $context = []): ResponseInterface;
}
