<?php 

namespace Hcode;

use Rain\Tpl;

class Page {

	private $tpl;
	private $options = [];
	private $defaults = [
		"data"=>[]
	];

	public function __construct($opts = array(), $tpl_dir = "/views/"){

		//array merge mescla 2 arrays - O último sempre sobrescreve os anteriores
		$this->options = array_merge($this->defaults, $opts);

		$config = array(
			"tpl_dir"       => $_SERVER["DOCUMENT_ROOT"].$tpl_dir,
			"cache_dir"     => $_SERVER["DOCUMENT_ROOT"]."/views-cache/",
			"debug"         => false // set to false to improve the speed
	   	);

		Tpl::configure( $config );		

		$this->tpl = new Tpl;

		$this->setData($this->options["data"]);

		$this->tpl->draw("header");

	}

	//Passar os dados para meu template
	private function setData($data = array())
	{

		foreach ($this->options["data"] as $key => $value) {
			//Variáveis que vão pegar no template
			$this->tpl->assign($key, $value);
		}

	}

	public function setTpl($name, $data = array(), $returnHtml = false)
	{

		$this->setData($data);

		return $this->tpl->draw($name, $returnHtml);

	}

	public function __destruct(){

		$this->tpl->draw("footer");

	}


}

 ?>