<?php 

use \Hcode\PageAdmin;
use \Hcode\Model\User;

$app->get('/admin/users', function() {

	User::verifyLogin();

	//Array com toda lista de usuários
	$users = User::listAll();

	$page = new PageAdmin();

	$page->setTpl("users", array(
		"users"=>$users
	));

});

$app->get('/admin/users/create', function() {

	User::verifyLogin();

	$page = new PageAdmin();

	$page->setTpl("users-create");

});

//como ele tem mais um /delete coloca em cima, a ordem importa nas rotas
$app->get('/admin/users/:iduser/delete', function($iduser) {

	//o usuário(admin) tem que estar logado
	User::verifyLogin();

	$user = new User();

	$user->get((int)$iduser);

	$user->delete();

	header("Location: /admin/users");
	exit;

});

//iduser parâmetro de rota - ele já entende que a função ta passando
$app->get('/admin/users/:iduser', function($iduser){

    User::verifyLogin();

    $user = new User();

    $user->get((int)$iduser);

    $page = new PageAdmin();

    $page ->setTpl("users-update", array(
 
        "user"=>$user->getValues()
 
    ));
});

$app->post('/admin/users/create', function() {

	//o usuário(admin) tem que estar logado
	User::verifyLogin();

	$user = new User();

	//se inadmin for definido é 1, senão é 0
	$_POST["inadmin"] = (isset($_POST["inadmin"]))?1:0;

	$user->setData($_POST);

	$user->save();

	header("Location: /admin/users");
	exit;

});

//Admin alterar usuário
$app->post('/admin/users/:iduser', function($iduser) {

	//o usuário(admin) tem que estar logado - verifica a sessão
	User::verifyLogin();

	$user = new User();

	//se inadmin for definido é 1, senão é 0
	$_POST["inadmin"] = (isset($_POST["inadmin"]))?1:0;

	$user->get((int)$iduser);

	//Chama o método setData do model e cria um set para cada campo=> name(users-update.html na View)
	$user->setData($_POST);

	$user->update();

	header("Location: /admin/users");
	exit;

});


 ?>