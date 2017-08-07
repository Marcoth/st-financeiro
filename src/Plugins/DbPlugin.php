<?php

declare(strict_types=1);

namespace STFin\Plugins;

use Interop\Container\ContainerInterface;
use STFin\Models\BillPay;
use STFin\Models\BillReceive;
use STFin\Models\CategoryCost;
use STFin\Models\DiscountLaunch;
use STFin\Models\User;
use STFin\Repository\CategoryCostRepository;
use STFin\Repository\DefaultRepository;
use STFin\Repository\RepositoryFactory;
use STFin\Repository\StatementRepository;
use STFin\Repository\StatementtRepository;
use STFin\ServiceContainerInterface;
use Illuminate\Database\Capsule\Manager as Capsule;

class DbPlugin implements PluginInterface
{
    /**
     * registra  as configurações de credencias do banco de dados com o eloquente
     */
    public function register(ServiceContainerInterface $container)
    {
        /**
         * configurando a capsula do eloquente para conexao com o Banco
         */
        $capsule = new Capsule();
        $config = include __DIR__ . '/../../config/db.php';
        $capsule->addConnection($config['default_connection']);
        /**
         * inicia o eloquente
         */
        $capsule->bootEloquent();
        /**
         * registrando a Factory e os repositorys no container de serviço
         */
        $container->add('repository.factory', new RepositoryFactory());
        $container->addLazy(
            'category-cost.repository', function () {
            //            return $container->get('repository.factory')->factory(CategoryCost::class);
            return new CategoryCostRepository();
        }
        );
        $container->addLazy(
            'bill-receive.repository', function (ContainerInterface $container) {
            return $container->get('repository.factory')->factory(BillReceive::class);
        }
        );
        $container->addLazy(
            'bill-pay.repository', function (ContainerInterface $container) {
            return $container->get('repository.factory')->factory(BillPay::class);
        }
        );
        $container->addLazy(
            'discount-launch.repository', function (ContainerInterface $container) {
            return $container->get('repository.factory')->factory(DiscountLaunch::class);
        }
        );
        $container->addLazy(
            'user.repository', function (ContainerInterface $container) {
            return $container->get('repository.factory')->factory(User::class);
        }
        );
        $container->addLazy(
            'statement.repository', function () {
            return new StatementRepository();
        }
        );
    }
}
