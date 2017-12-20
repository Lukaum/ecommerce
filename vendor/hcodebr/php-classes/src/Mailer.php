<?php 

//Vamos fazer a renderização do phpMailer vai ser feita com o RainTpl

namespace Hcode;

use Rain\Tpl;

class Mailer {

	//dados
	const USERNAME = "";
	const PASSWORD = "";
	const NAME_FROM = "Hcode Store";

	private $mail;

	public function __construct($toAddress, $toName, $subject, $tplName, $data = array())
	{

		$config = array(
			"tpl_dir"       => $_SERVER["DOCUMENT_ROOT"]."/views/email/",
			"cache_dir"     => $_SERVER["DOCUMENT_ROOT"]."/views-cache/",
			"debug"         => false // set to false to improve the speed
	   	);

		Tpl::configure( $config );		

		//Instancio o tpl
		$tpl = new Tpl;		

		//pra criar as variáveis dentro do template
		foreach ($data as $key => $value) {
			$tpl->assign($key, $value);
		}

		//passamos o true para ele não jogar na tela e sim na variável
		$html = $tpl->draw($tplName, true);

		//Create a new PHPMailer instance
		$this->mail = new \PHPMailer;

		//Tell PHPMailer to use SMTP
		$this->mail->isSMTP();

		//Para funcionar tem que ter isso
		$this->mail->SMTPOptions = array(
		'ssl' => array(
		    'verify_peer' => false,
		    'verify_peer_name' => false,
		    'allow_self_signed' => true
		));

		//Enable SMTP debugging
		// 0 = off (for production use)
		// 1 = client messages
		// 2 = client and server messages
		$this->mail->SMTPDebug = 0;

		//Set the hostname of the mail server
		$this->mail->Host = 'smtp.gmail.com';
		// use
		// $this->mail->Host = gethostbyname('smtp.gmail.com');
		// if your network does not support SMTP over IPv6

		//Set the SMTP port number - 587 for authenticated TLS, a.k.a. RFC4409 SMTP submission
		$this->mail->Port = 587;

		//Set the encryption system to use - ssl (deprecated) or tls
		$this->mail->SMTPSecure = 'tls';

		//Whether to use SMTP authentication
		$this->mail->SMTPAuth = true;

		//Username to use for SMTP authentication - use full email address for gmail
		//Criado a constante USERNAME
		$this->mail->Username = Mailer::USERNAME;

		//Password to use for SMTP authentication
		$this->mail->Password = Mailer::PASSWORD;

		//Set who the message is to be sent from
		$this->mail->setFrom(Mailer::USERNAME, Mailer::NAME_FROM);

		//Set who the message is to be sent to
		$this->mail->addAddress($toAddress, $toName);

		//Set the subject line
		$this->mail->Subject = $subject;

		//Read an HTML message body from an external file, convert referenced images to embedded,
		//convert HTML into a basic plain-text alternative body
		//$html vai ser renderizado com o Raintpl
		$this->mail->msgHTML($html);

		//Replace the plain text body with one created manually
		$this->mail->AltBody = 'This is a plain-text message body';

		//Attach an image file
		//$this->mail->addAttachment('images/phpmailer_mini.png');

		//send the message, check for errors
		/*
			if (!$this->mail->send()) {
			    echo "Mailer Error: " . $mail->ErrorInfo;
			} else {
			    echo "Message sent!";
			}
		*/
			
	}

	public function send()
	{

		return $this->mail->send();

	}

}

 ?>