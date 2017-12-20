<?php 

session_start();

//require do composer
require_once("vendor/autoload.php");

//Namespaces - dentro do vendor eu tenho dezenas de classes
use \Slim\Slim;

//Configurado e chamando o slim - nova aplicação para as rotas (mando o nome para a url e ele me manda para algum lugar)
$app = new Slim();

//Chamando a aplicação com modo debug, para mostrar erros
$app->config('debug', true);

require_once("site.php");
require_once("admin.php");
require_once("admin-users.php");
require_once("admin-categories.php");
require_once("admin-products.php");

$app->run();

 ?>