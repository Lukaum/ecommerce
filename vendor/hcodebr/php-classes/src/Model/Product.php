<?php 

//crio meu namespace
namespace Hcode\Model;

//chama o namespacedo da classe Sql.php
use \Hcode\DB\Sql;
use \Hcode\Model;
use \Hcode\Mailer;

class Product extends Model {

	//Lista todas as categorias - Estático não preciso instanciar a classe lá no index.php
	//Porém 
	public static function listAll()
	{

		$sql = new Sql();

		return $sql->select("SELECT * FROM tb_products ORDER BY desproduct");

	}

	public function save()
	{

		$sql = new Sql();

		$results = $sql->select("CALL sp_products_save(:pidproduct, :pdesproduct, :pvlprice, :pvlwidth, :pvlheight, :pvllength, :pvlweight, :pdesurl)", array(
			":pidproduct"=>$this->getidproduct(),
			":pdesproduct"=>$this->getdesproduct(),
			":pvlprice"=>$this->getvlprice(),
			":pvlwidth"=>$this->getvlwidth(),
			":pvlheight"=>$this->getvlheight(),
			":pvllength"=>$this->getvllength(),
			":pvlweight"=>$this->getvlweight(),
			":pdesurl"=>$this->getdesurl()
		));

		//Método do Model.php
		$this->setData($results[0]);
		
	}

	public function get($idproduct)
	{

		$sql = new Sql();

		$results = $sql->select("SELECT * FROM tb_products WHERE idproduct = :idproduct", [
			':idproduct'=>$idproduct
		]);

		$this->setData($results[0]);

	}

	public function delete()
	{

		$sql = new Sql();

		//como aqui eu não tenho o parâmetro, ele pega do get
		$sql->query("DELETE FROM tb_products WHERE idproduct = :idproduct", [
			':idproduct'=>$this->getidproduct()
		]);

	}

	public function checkPhoto()
	{

		$sql = new Sql();

		//pesquisa para verificar se existem imagens no banco
		$resultsExistPhoto = $sql->select("SELECT * FROM tb_productsphoto WHERE idproduct = :idproduct", [
			':idproduct'=>$this->getidproduct()
		]);

		$countResultsPhoto = count($resultsExistPhoto);

		if ($countResultsPhoto > 0)
		{

			//For que cria um array de todas as imagens do produto no banco
			for ($arq = 0; $arq < $countResultsPhoto; $arq++)
			{

				//AQUI USAMOS / PORQUE É URL
				//$urlInArray[] = "/res/site/img/products/". $resultsExistPhoto[$arq]['idproduct'] . "-" . $resultsExistPhoto[$arq]['idphoto'] . ".jpg";

				$urlInArray[]['products'] = '/res/site/img/products/'. $resultsExistPhoto[$arq]['idproduct'] . "-" . $resultsExistPhoto[$arq]['idphoto'] . '.jpg';

				$urlArray = $urlInArray;

			} 


			$url = array_column($urlArray, 'products');

			var_dump($urlArray);
			exit;

			return $urlArray;

		}
		else
		{

			$url = array("/res/site/img/product.jpg");

			return $this->setdesphoto($url);
			
		}

	}

	public function getValues()
	{

		$this->checkPhoto();

		$values = parent::getValues();

		return $values;

	}

	public function setPhoto($file, $idproduct)
	{

		//procurar ponto do arquivo e fazer um array dele
		/*
		$extension = explode('.', $file['name']);
		var_dump($extension);
		exit;
		$extension = end($extension);

		switch ($extension) {
			case "jpg":
			case "jpeg":
				$image = imagecreatefromjpeg($file["tmp_name"]);
			break;

			case "gif":
				$image = imagecreatefromgif($file["tmp_name"]);
			break;

			case "png":
				$image = imagecreatefrompng($file["tmp_name"]);
			break;
			
		}

		$dist = $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . 
			"res" . DIRECTORY_SEPARATOR . 
			"site" . DIRECTORY_SEPARATOR . 
			"img" . DIRECTORY_SEPARATOR . 
			"products" . DIRECTORY_SEPARATOR .
			$this->getidproduct() . ".jpg";

		imagejpeg($image, $dist);

		imagedestroy($image);

		$this->checkPhoto();
		*/

		//Pega as imagens enviadas no http://www.hcodecommerce.com.br/admin/products/4(:idproduct)
		$file = $_FILES['file'];
		//array filter retorna 0, caso não haja nenhuma img
		$numFile = count(array_filter($file['name']));

		//Se existir imagem uploadada ele entra na condição
		if ($numFile > 0)
		{	

			$sql = new Sql();

			//pesquisa para verificar se já existe um mesmo registro no banco
			$resultsExistPhoto = $sql->select("SELECT * FROM tb_productsphoto WHERE idproduct = :idproduct", [
				':idproduct'=>$idproduct
			]);

			$timestampNow = time();

			$countResultsPhoto = count($resultsExistPhoto);
		
			//For que deleta as imagens antigas da pasta e do banco de dados
			for ($arq = 0; $arq < $countResultsPhoto; $arq++)
			{

				if ($timestampNow > $resultsExistPhoto[$arq]['timephoto'])
				{

					$filename = $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . 
						"res" . DIRECTORY_SEPARATOR . 
						"site" . DIRECTORY_SEPARATOR . 
						"img" . DIRECTORY_SEPARATOR . 
						"products" . DIRECTORY_SEPARATOR .
						$resultsExistPhoto[$arq]['idproduct']."-".$resultsExistPhoto[$arq]['idphoto'].".jpg";

					//var_dump($filename);
					//exit;

					//Garante o nível de permissão (chmod) para excluir a foto e exclui a foto (unlink) // Rever permissão chmod depois
					chmod($filename, 0777);
					unlink($filename);

					$resultsDeletePhoto = $sql->query("DELETE FROM tb_productsphoto WHERE idproduct = :idproduct", [
						':idproduct'=>$resultsExistPhoto[$arq]['idproduct']
					]);

				}

			}		

			//For para verificar a quantidade de imagens (atuais) enviadas
			for ($i = 0; $i <= $numFile; $i++)
			{

				if (isset($file['name'][$i]))
				{
				
					$name = $file['name'][$i];

					$extension = explode(".", $name);
					$extension = end($extension);

					switch ($extension) {
						
						case "jpg":
						case "jpeg":
							$image = imagecreatefromjpeg($file["tmp_name"][$i]);
						break;

						case "gif":
							$image = imagecreatefromgif($file["tmp_name"][$i]);
						break;

						case "png":
							$image = imagecreatefrompng($file["tmp_name"][$i]);
						break;
						
					}

					$countImg = $i+1;
					$namePhoto = $this->getidproduct(). "-" . $countImg . ".jpg";

					$dist = $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . 
						"res" . DIRECTORY_SEPARATOR . 
						"site" . DIRECTORY_SEPARATOR . 
						"img" . DIRECTORY_SEPARATOR . 
						"products" . DIRECTORY_SEPARATOR .
						$namePhoto;

					imagejpeg($image, $dist);

					imagedestroy($image);				

					$sql->query("INSERT INTO tb_productsphoto (idproduct, idphoto, namephoto, timephoto) VALUES (:idproduct, :idphoto, :namephoto, :timephoto)", [
						":idproduct"=>$this->getidproduct(),
						":idphoto"=>$countImg,
						":namephoto"=>$namePhoto,
						":timephoto"=>$timestampNow
					]);		

					$this->checkPhoto();

				}		

			}

		}

	}

}

 ?>