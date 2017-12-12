<?php 

//require do composer
require_once("vendor/autoload.php");

//Namespaces - dentro do vendor eu tenho dezenas de classes
use \Slim\Slim;
use \Hcode\Page;

//Configurado e chamando o slim - nova aplicação para as rotas (mando o nome para a url e ele me manda para algum lugar)
$app = new Slim();

//Chamando a aplicação com modo debug, para mostrar erros
$app->config('debug', true);

//Rota com / a principal - Quando meu site tiver sem nenhum tipo de rota, carrega isso aqui
$app->get('/', function() {

	$page = new Page();

	$page->setTpl("index");

});

$app->run();

 ?>