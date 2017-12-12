<?php 

//crio meu namespace
namespace Hcode\Model;

//chama o namespacedo da classe Sql.php
use \Hcode\DB\Sql;
use \Hcode\Model;

class User extends Model {

	const SESSION = "User";

	public static function login($login, $password)
	{

		$sql = new Sql();

		$results = $sql->select("SELECT * FROM tb_users WHERE deslogin = :LOGIN", array(
			":LOGIN"=>$login
		));

		if (count($results) === 0)
		{
			//Coloco \ pra ele achar a exeption do php, já que não criamos ainda
			throw new \Exception("Usuário inexistente ou senha inválida.");
		}

		//registros do banco
		$data = $results[0];

		//verifica a hash do banco
		if (password_verify($password, $data["despassword"]) === true)
		{

			$user = new User();

			//Passo o array inteiro que o banco retornou
			$user->setData($data);

			$_SESSION[User::SESSION] = $user->getValues();

			return $user;

		} else {
			//Coloco \ pra ele achar a exeption do php, já que não criamos ainda
			throw new \Exception("Usuário inexistente ou senha inválida.");	
		}

	}

	public static function verifyLogin($inadmin = true)
	{

		if (
			!isset($_SESSION[User::SESSION])
			||
			!$_SESSION[User::SESSION]
			||
			!(int)$_SESSION[User::SESSION]["iduser"] > 0
			||
			(bool)$_SESSION[User::SESSION]["inadmin"] !== $inadmin
		) {

			header("Location: /admin/login");
			exit;

		}

	}

	public static function logout()
	{

		$_SESSION[User::SESSION] = NULL;

	}

}

 ?>