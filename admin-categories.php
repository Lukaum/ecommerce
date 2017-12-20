<?php 

use \Hcode\PageAdmin;
use \Hcode\Model\User;
use \Hcode\Model\Category;

$app->get("/admin/categories", function(){

	//o usuário(admin) tem que estar logado - verifica a sessão
	User::verifyLogin();

	$categories = Category::listAll();

	$page = new PageAdmin();

	//Chamo $categories no html categories.html
	$page->setTpl("categories", [
		'categories'=>$categories
	]);

});

$app->get("/admin/categories/create", function(){

	//o usuário(admin) tem que estar logado - verifica a sessão
	User::verifyLogin();

	$page = new PageAdmin();

	//Chamo $categories no html categories.html
	$page->setTpl("categories-create");

});

$app->post("/admin/categories/create", function(){

	//o usuário(admin) tem que estar logado - verifica a sessão
	User::verifyLogin();

	$category = new Category();

	//vamos setar a variavel post(html) e vai colocar no objeto
	$category->setData($_POST);

	$category->save();

	header("Location: /admin/categories");
	exit;

});

$app->get("/admin/categories/:idcategory/delete", function($idcategory){

	//o usuário(admin) tem que estar logado - verifica a sessão
	User::verifyLogin();

	$category = new Category();

	$category->get((int)$idcategory);

	$category->delete();

	header("Location: /admin/categories");
	exit;

});

$app->get("/admin/categories/:idcategory", function($idcategory){

	//o usuário(admin) tem que estar logado - verifica a sessão
	User::verifyLogin();

	$category = new Category();

	$category->get((int)$idcategory);

	$page = new PageAdmin();

	//no action de categories-update.html eu chamo $category, passando o valor do obj category, assim com transformo em array
	$page->setTpl("categories-update", [
		'category'=>$category->getValues()
	]);

});

$app->post("/admin/categories/:idcategory", function($idcategory){

	//o usuário(admin) tem que estar logado - verifica a sessão
	User::verifyLogin();

	$category = new Category();

	$category->get((int)$idcategory);

	//Como já gero o padrão do banco, então não preciso setar um por um (como teria que ser feito)
	$category->setData($_POST);

	$category->save();

	header("Location: /admin/categories");
	exit;

});

$app->get("/categories/:idcategory", function($idcategory){

	$category = new Category();

	$category->get((int)$idcategory);

	$page = new Page();

	$page->setTpl("category", [
		'category'=>$category->getValues(),
		'products'=>[]
	]);

});

 ?>