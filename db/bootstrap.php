<?php
use STFin\Application;
use STFin\Plugins\AuthPlugin;
use STFin\Plugins\DbPlugin;
use STFin\ServiceContainer;

/** @var  $serviceContainer - instacia do container de servicos */
$serviceContainer = new ServiceContainer();
/** @var  $app - instancia de application passando o serviceContainer */
$app = new Application($serviceContainer);
/** plugin para conexão */
$app->plugin(new DbPlugin());
/** plugin para autenticação */
$app->plugin(new AuthPlugin());

return $app;