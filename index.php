<?php 

session_start();

//require do composer
require_once("vendor/autoload.php");

//Namespaces - dentro do vendor eu tenho dezenas de classes
use \Slim\Slim;
use \Hcode\Page;
use \Hcode\PageAdmin;
use \Hcode\Model\User;

//Configurado e chamando o slim - nova aplicação para as rotas (mando o nome para a url e ele me manda para algum lugar)
$app = new Slim();

//Chamando a aplicação com modo debug, para mostrar erros
$app->config('debug', true);

//Rota com / a principal - Quando meu site tiver sem nenhum tipo de rota, carrega isso aqui
$app->get('/', function() {

	$page = new Page();

	$page->setTpl("index");

});

//Rota admin
$app->get('/admin', function() {

	User::verifyLogin();

	$page = new PageAdmin();

	$page->setTpl("index");

});

//Página de login o footer,header e conteudo não estão separados
$app->get('/admin/login', function(){

	//desabilito o header e o footer padrão
	$page = new PageAdmin([
		"header"=>false,
		"footer"=>false
	]);

	$page->setTpl("login");

});

//Post login admin
$app->post('/admin/login', function(){

	//método estático chamado login
	User::login($_POST["login"], $_POST["password"]);

	//Se ele não der erro ele redireciona para a homepage da administração
	header("Location: /admin");
	exit;

});

$app->get('/admin/logout', function() {

	User::logout();

	header("Location: /admin/login");
	exit;
	
});

$app->run();

 ?>