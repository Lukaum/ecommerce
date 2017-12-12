<?php 

namespace Hcode;

class PageAdmin extends Page {

	public function __construct($opts = array(), $tpl_dir = "/views/admin/")
	{

		//já aproveito e chamo o método construtor do Page - Somente métodos protegidos e publicos
		parent::__construct($opts, $tpl_dir);

	}

}


 ?>