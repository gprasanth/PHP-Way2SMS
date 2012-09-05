<?php

class PHPWay2SMS{

	private $username;
	private $password;
	private $useragent = "Mozilla/5.0 (Windows; U; Windows NT 6.0; en-US; rv:1.9.0.5) Gecko/2008120122 Firefox/3.0.5";
	private $proxy = "";
	private $num;

	public function __construct($username, $password, $useragent="", $proxy=""){
		$this->username 	= $username;
		$this->password 	= $password;
		$this->useragent 	= $useragent ? $useragent : $this->useragent;
		$this->proxy 		= $proxy;
		if(!function_exists("curl_init")){
			die("No cURL!");
		}
		$this->prepare();
		$this->login();
	}

	public function prepare(){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "http://www.way2sms.com");
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		if($this->proxy != ""){
			curl_setopt($ch, CURLOPT_PROXY, $this->proxy);
			curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, 0);
		}
		$res = curl_exec($ch);
		$str = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
		if(preg_match("/site(\d+)\.way2sms\.com/",$str,$ds)){
			$this->num = $ds[1];
		}else{
			$this->num = 2;
		}
		curl_close($ch);
	}

	public function login(){
		$ch = curl_init();
		curl_setopt ($ch, CURLOPT_URL, "http://site".$this->num.".way2sms.com/Login1.action");
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt ($ch, CURLOPT_POST, 1);
		curl_setopt ($ch, CURLOPT_POSTFIELDS, "username=" . $this->username . "&password=" . $this->password . "&userLogin=yes&message=&mobileNo=");
		if($this->proxy != ""){
			curl_setopt($ch, CURLOPT_PROXY, $this->proxy);
			curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, 0);
		}
		curl_setopt ($ch, CURLOPT_COOKIESESSION, 1);
		curl_setopt ($ch, CURLOPT_COOKIEFILE, dirname(__FILE__).DIRECTORY_SEPARATOR."cookie_way2sms.txt");
		curl_setopt ($ch, CURLOPT_COOKIEJAR, dirname(__FILE__).DIRECTORY_SEPARATOR."cookie_way2sms.txt");
		curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt ($ch, CURLOPT_MAXREDIRS, 5);
		curl_setopt ($ch, CURLOPT_USERAGENT, $this->useragent);
		curl_setopt ($ch, CURLOPT_REFERER, "http://site".$this->num.".way2sms.com/content/index.html");
		$text = curl_exec($ch);
		$res = stripos(curl_getinfo($ch, CURLINFO_EFFECTIVE_URL), "Main.action");
		if($res === false){
			die("Invalid login!");
		}
		curl_close($ch);
	}

	public function send($number, $message){
		$url = "http://site".$this->num.".way2sms.com/quicksms.action";
		$fields = array(
			'Action' => 'dsf45asvd5',
			'HiddenAction' => 'instantsms',
			'MobNo' => $number,
			'catnamedis' => 'Birthday',
			'chkall' => 'on',
			'textArea' => $message,
			);

		$fields_string = "";
		foreach($fields as $key=>$value) { $fields_string .= $key.'='.urlencode($value).'&'; }
		$fields_string = rtrim($fields_string,'&');

		$ch = curl_init();
		curl_setopt ($ch, CURLOPT_URL, $url);
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 1);
		if($this->proxy != ""){
			curl_setopt($ch, CURLOPT_PROXY, $this->proxy);
			curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, 0);
		}
		// curl_setopt ($ch, CURLOPT_COOKIESESSION, 1);
		curl_setopt ($ch, CURLOPT_COOKIEFILE, dirname(__FILE__).DIRECTORY_SEPARATOR."cookie_way2sms.txt");
		curl_setopt ($ch, CURLOPT_COOKIEJAR, dirname(__FILE__).DIRECTORY_SEPARATOR."cookie_way2sms.txt");
		curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt ($ch, CURLOPT_MAXREDIRS, 5);
		curl_setopt ($ch, CURLOPT_USERAGENT, $this->useragent);
		curl_setopt ($ch, CURLOPT_REFERER, "http://site".$this->num.".way2sms.com/jsp/InstantSMS.jsp");
		curl_setopt ($ch, CURLOPT_POST,1);
		curl_setopt ($ch, CURLOPT_POSTFIELDS,$fields_string);

		$text = curl_exec($ch);
		echo htmlspecialchars($text);
		curl_close($ch);
		unset($ch);
	}
}

/*
 * Usage
 */
// $sender = new PHPWay2SMS("9876543210","password123");
// $sender->send('0123456789', 'Test message!');

?>
