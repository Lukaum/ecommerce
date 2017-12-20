<?php 

use \Hcode\Page;

//Rota com / a principal - Quando meu site tiver sem nenhum tipo de rota, carrega isso aqui
$app->get('/', function() {

	$page = new Page();

	$page->setTpl("index");

});




 ?>