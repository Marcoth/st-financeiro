<?php
/**  primeiro passo é fazer a chamada do autoload do composer para que tudo seja carregado */
require __DIR__ . '/vendor/autoload.php';

if(file_exists(__DIR__ .'/.env')) {
    $dotenv = new \Dotenv\Dotenv(__DIR__);
    $dotenv->overload();
}
//pega as informações de conexao criada (DIR) indica o caminho absoluto
$db = include __DIR__ . '/config/db.php';
list(
    'driver' => $adapter,
    'host' => $host,
    'database' => $name,
    'username' => $user,
    'password' => $pass,
    'charset' => $charset,
    'collation' => $collation
    ) = $db['default_connection'];
/**
 *@paths - aponta qual o caminho ele vai gerar as migrações,
 *@environments- configurações de ambiente rela tivo ao banco de dados
 *@default_migration_table - qual o nome da tabela que ele vai controlar
e quais migracoes ja foram executadas no banco de dados
 *@default_database - qual configuração com o banco de dados
 */
return [
    'paths' => [
        'migrations' => [
            __DIR__ . '/db/migrations'
        ],
        'seeds' => [
            __DIR__ . '/db/seeds'
        ]
    ],
    'environments' => [
        'default_migration_table' => 'migrations',
        'default_database' => 'default_connection',
        'default_connection' => [
            'adapter' => $adapter,
            'host' => $host,
            'name' => $name,
            'user' => $user,
            'pass' => $pass,
            'charset' => $charset,
            'collation' => $collation
        ]
    ]
];