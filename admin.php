<?php 

use \Hcode\PageAdmin;
use \Hcode\Model\User;

//Rota admin Quando estiver logado - Início
$app->get('/admin', function() {

	User::verifyLogin();

	$page = new PageAdmin();

	$page->setTpl("index");

});

////Rota admin Tela de Login => Página de login o footer,header e conteudo não estão separados
$app->get('/admin/login', function() {

	//desabilito o header e o footer padrão
	$page = new PageAdmin([
		"header"=>false,
		"footer"=>false
	]);

	$page->setTpl("login");

});

//Post login admin
$app->post('/admin/login', function() {

	//método estático da classe User chamando a função login
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

/*ADMIN ESQUECEU A SENHA*/

//Rota página forgot
$app->get('/admin/forgot', function() {

	$page = new PageAdmin([
		"header"=>false,
		"footer"=>false
	]);

	$page->setTpl("forgot");

});

//Post pagina forgot
$app->post('/admin/forgot', function() {

	$user = User::getForgot($_POST["email"]);

	header("Location: /admin/forgot/sent");
	exit;

});

//Rota email enviado
$app->get('/admin/forgot/sent',function(){

	$page = new PageAdmin([
		"header"=>false,
		"footer"=>false
	]);

	$page->setTpl("forgot-sent");

});

$app->get("/admin/forgot/reset", function(){

	$user = User::validForgotDecrypt($_GET["code"]);

	$page = new PageAdmin([
		"header"=>false,
		"footer"=>false
	]);

	$page->setTpl("forgot-reset", array(
		"name"=>$user["desperson"],
		"code"=>$_GET["code"]
	));

});

$app->post("/admin/forgot/reset", function(){

	$forgot = User::validForgotDecrypt($_POST["code"]);

	User::setForgotUsed($forgot["idrecovery"]);

	$user = new User();

	$user->get((int)$forgot["iduser"]);

	//cost é a força da criptografia, porém quanto maior o valor, necessita de mais processamento do servidorm portanto 12 é um numero bom
	$password = password_hash($_POST["password"], PASSWORD_DEFAULT, [
		"cost"=>12
	]);

	//Como precisamos fazer o hash, então temos que criar esse set, pois no modelo que temos que gera automaticamente não é possível fazer isso
	$user->setPassword($password);

	$page = new PageAdmin([
		"header"=>false,
		"footer"=>false
	]);

	//forgot-reset-success é o nome do arquivo; Como não preciso de variável nenhuma nessa pagina (post ou get) então não preciso de array
	$page->setTpl("forgot-reset-success");

});


 ?>