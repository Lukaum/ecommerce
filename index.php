<?php 

//require do composer
require_once("vendor/autoload.php");

//Configurado e chamando o slim
$app = new \Slim\Slim();

//Chamando a aplicação com modo debug, para mostrar erros
$app->config('debug', true);

//Rota com / a principal
$app->get('/', function() {

    //Mando executar a rota - Entro no vendor/Hcode e no namespace DB
	$sql = new Hcode\DB\Sql();

	$results = $sql->select("SELECT * FROM tb_users");
	
	echo json_encode($results);

});

$app->run();

 ?>