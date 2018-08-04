<?php

require_once('../vendor/autoload.php');
$client=GraphAware\Neo4j\Client\ClientBuilder::create()->addConnection('bolt', 'bolt://php:CodeAccess12@localhost:7687')->build();
$password = $email = null; 
$status = ['status' => 1, 'fields' => ['password' => 1, 'email' => 1]]; 
if($_SERVER['REQUEST_METHOD'] = 'POST'){
	if(isset($_POST['password']) && !empty($_POST['password'])){
		$status['fields']['password'] = 0; 
		$password = $_POST['password'];
	}
	else{
		$status['fields']['password'] = 2; 
	}
	if(isset($_POST['email']) && !empty($_POST['email'])){ 
		$return = $client -> run('MATCH (a:Account) WHERE a.email = {email} RETURN COUNT(a) AS count', ['email' => $_POST['email']]);
		if($return -> getRecord() -> value('count') > 0){
			$status['fields']['email'] = 0;
			$email = $_POST['email'];
		}
		else{
			$status['fields']['email'] = 3;
		}
	}
	else{
		$status['fields']['email'] = 2; 
	}
	if($status['fields']['email'] === 0 && $status['fields']['password'] === 0){
		$return = $client -> run('MATCH (a:Account) WHERE a.email = {email} RETURN a.password AS password', ['email' => $email]);
		if(password_verify($password, $return -> getRecord() -> value('password'))){
			session_start();
			$_SESSION['myEmail'] = $email;
			$status['status'] = 0;
		}
		else{
			$status['status'] = 4;
		}
	}
	else{
		$status['status'] = 3;
	}
}
else{
	$status['status'] = 2;
}

header('Content-Type: application/json;charset=utf-8');
echo(json_encode($status)); 
?>