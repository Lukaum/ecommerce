<?php 

//ela está no namespace principal
namespace Hcode;

class Model {

	//todos os valores que temos no campo do objeto
	private $values = [];

	//Pra isso temos que saber toda vez que um método for chamado (get ou set), para isso temos o metodo mágico call
	public function __call($name, $args)
	{

		//$method pra saber qual método foi chamado
		//A partir da posição 0, traga 0,1 e 2
		$method = substr($name, 0, 3);

		//Pra saber o nome do campo, dou um strlen pra começar a partir do 3 até o final
		$fieldName = substr($name, 3, strlen($name));

		switch ($method) 
		{

			//somente retorna o valor contido no fieldName
			//isset verifica se ja foi definido, caso sim ele retorna, caso não retorna null
			case "get":
				return (isset($this->values[$fieldName])) ? $this->values[$fieldName] : NULL;
			break;
			//Args pega o valor
			case "set":
				$this->values[$fieldName] = $args[0];
			break;
		
		}

	}

	public function setData($data = array())
	{

		foreach ($data as $key => $value) {

			//Como estou criando dinamicamente(então o id usuário teria que ter um get e um set), então por isso tenho que colocar em {""}
			//ele cria o set de cada elemento HTML "name" passado por POST
			$this->{"set".$key}($value);

		}

	}

	public function getValues()
	{

		return $this->values;

	}

}

 ?>