<?php 

//crio meu namespace
namespace Hcode\Model;

//chama o namespacedo da classe Sql.php
use \Hcode\DB\Sql;
use \Hcode\Model;
use \Hcode\Mailer;

class User extends Model {

	const SESSION = "User";

	//Chave com 16 caracteres
	const SECRET = "HcodePhp7_Secret";

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

		//registros id do banco
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

	public static function listAll()
	{

		$sql = new Sql();

		return $sql->select("SELECT * FROM tb_users a INNER JOIN tb_persons b USING(idperson) ORDER BY b.desperson");

	}

	public function save()
	{

		$sql = new Sql();

		$results = $sql->select("CALL sp_users_save(:desperson, :deslogin, :despassword, :desemail, :nrphone, :inadmin)", array(
			":desperson"=>$this->getdesperson(),
			":deslogin"=>$this->getdeslogin(),
			":despassword"=>$this->getdespassword(),
			":desemail"=>$this->getdesemail(),
			":nrphone"=>$this->getnrphone(),
			":inadmin"=>$this->getinadmin()
		));

		$this->setData($results[0]);

	}

	public function get($iduser)
	{
	 
	    $sql = new Sql();
	 
	    $results = $sql->select("SELECT * FROM tb_users a INNER JOIN tb_persons b USING(idperson) WHERE a.iduser = :iduser", array(
	        ":iduser"=>$iduser
	    ));
	 
	 	//primeiro registro
	    $data = $results[0];
	 
	    $data['desperson'] = utf8_encode($data['desperson']);
	 
	 
	    $this->setData($data);
	 
	}	

	public function update()
	{

		$sql = new Sql();

		$results = $sql->select("CALL sp_usersupdate_save(:iduser, :desperson, :deslogin, :despassword, :desemail, :nrphone, :inadmin)", array(
			":iduser"=>$this->getiduser(),
			":desperson"=>$this->getdesperson(),
			":deslogin"=>$this->getdeslogin(),
			":despassword"=>$this->getdespassword(),
			":desemail"=>$this->getdesemail(),
			":nrphone"=>$this->getnrphone(),
			":inadmin"=>$this->getinadmin()
		));

		$this->setData($results[0]);

	}

	public function delete()
	{

		$sql = new Sql();

		$sql->query("CALL sp_users_delete(:iduser)", array(
			":iduser"=>$this->getiduser()
		));

	}

	public static function getForgot($email)
	{

		$sql = new Sql();

		$results = $sql->select("
			SELECT *
			FROM tb_persons a
			INNER JOIN tb_users b USING(idperson)
			WHERE a.desemail = :email
		", array(
			":email"=>$email
		));

		if (count($results) === 0)
		{

			throw new \Exception("Não foi possível recuperar a senha");
			
		}
		else
		{

			//pega o id do usuário (posição 0 = 1º posição)
			$data = $results[0];

			$results2 = $sql->select("CALL sp_userspasswordsrecoveries_create(:iduser, :desip)", array(
				":iduser"=>$data["iduser"],
				":desip"=>$_SERVER["REMOTE_ADDR"]
			));

			if (count($results2) === 0)
			{

				//Não facilitar por causa da segurança, então mesma exception que lá em cima
				throw new \Exception("Não foi possível recuperar a senha1");
				
			} 
			else
			{

				$dataRecovery = $results2[0];

				//transforma código em texto; mcrypt faz a criptografia usando RIJNDAEL
				//uso o dataRecovery na coluna idrecovery do banco de dados
				//mode = 
				$code = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_128, User::SECRET, $dataRecovery["idrecovery"], MCRYPT_MODE_ECB));

				//link com código para envio por email
				$link = "http://www.hcodecommerce.com.br/admin/forgot/reset?code=$code";

				$mailer = new Mailer($data["desemail"], $data["desperson"], "Redefinir Senha da Hcode Store", "forgot", array(
					"name"=>$data["desperson"],
					"link"=>$link
				));

				$mailer->send();

				//retorno de data com os dados do usuário que foi recuperado, caso o método precise de alguma coisa
				return $data;

			}

		}

	}

	public static function validForgotDecrypt($code)
	{

		//Agora fazemos o processo inverso. Primeiro decripto o base64, depois preciso usar o inverso do mcrypt=>decrypt
		$idrecovery = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, User::SECRET, base64_decode($code), MCRYPT_MODE_ECB);

		$sql = new Sql();

		//Pesquisa que verifica o codigo, se ele não é nullo e também se passou ou não de 1 hora, a partir do NOW() da data atual
		$results = $sql->select("
			SELECT * 
			FROM tb_userspasswordsrecoveries a
			INNER JOIN tb_users b USING(iduser)
			INNER JOIN tb_persons c USING(idperson)
			WHERE 
				a.idrecovery = :idrecovery
			    AND
			    a.dtrecovery IS NULL
			    AND
			    DATE_ADD(a.dtregister, INTERVAL 1 HOUR) >= NOW();
		", array(
			"idrecovery"=>$idrecovery
		));

		if (count($results) === 0)
		{

			throw new \Exception("Não foi possível recuperar a senha.");
			
		} 
		else
		{

			//retorno os dados do banco de dados
			return $results[0];

		}


	}

	public static function setForgotUsed($idrecovery)
	{

		$sql = new Sql();

		$sql->query("UPDATE tb_userspasswordsrecoveries SET dtrecovery = NOW() WHERE idrecovery = :idrecovery", array(
			":idrecovery"=>$idrecovery
		));


	}

	public function setPassword($password)
	{

		$sql = new Sql();

		//getiduser é o método que temos gerado já
		$sql->query("UPDATE tb_users SET despassword = :password WHERE iduser = :iduser", array(
			":password"=>$password,
			":iduser"=>$this->getiduser()
		));

	}

}

 ?>