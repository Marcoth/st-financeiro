<?php
/** carrega toda as depedencias da aplicação */

use Psr\Http\Message\ServerRequestInterface;
use STFin\Application;
use STFin\Plugins\AuthPlugin;
use STFin\Plugins\DbPlugin;
use STFin\Plugins\RoutePlugin;
use STFin\Plugins\ViewPlugin;
use STFin\ServiceContainer;

require_once __DIR__ . '/../vendor/autoload.php';
/** @var  $dotenv - instancia da classe responsavel por carregar
 * as variaveis de ambiente para acesso ao banco de dados
 */
if (file_exists(__DIR__ . '/../.env')) {
    $dotenv = new \Dotenv\Dotenv(__DIR__ . '/../');
    $dotenv->overload();
}

require_once __DIR__ . '/../src/helpers.php';
/** @var  $serviceContainer - instacia do container de servicos */
$serviceContainer = new ServiceContainer();
/** @var  $app - instancia de application passando o serviceContainer */
$app = new Application($serviceContainer);

/** plugando o plugin de Rotas*/
/** plugando o plugin de view */
/** plugin para conexão */
/** plugin para autenticação */

$app->plugin(new RoutePlugin());
$app->plugin(new ViewPlugin());
$app->plugin(new DbPlugin());
$app->plugin(new AuthPlugin());

/** redirecionamento para tela de login  */
$app->get('/', function (ServerRequestInterface $request) use ($app) {
    return $app->route('auth.show_login_form');
});

/** incluindo controller de categoria de custos */
/** incluindo controller de usuários */
/** incluindo controller de autenticacao */
/** incluindo controller de contas a receber  */
/** incluindo controller de contas a pagar  */
/** incluindo controller de lançamentos de descontos  */
/** incluindo controller de extrato */
/** incluindo controller de gráficos */

require_once __DIR__ . '/../src/controllers/category-costs.php';
require_once __DIR__ . '/../src/controllers/users.php';
require_once __DIR__ . '/../src/controllers/auth.php';
require_once __DIR__ . '/../src/controllers/bill-receives.php';
require_once __DIR__ . '/../src/controllers/bill-pays.php';
require_once __DIR__ . '/../src/controllers/discount-launch.php';
require_once __DIR__ . '/../src/controllers/statements.php';
require_once __DIR__ . '/../src/controllers/charts.php';

$app->start();