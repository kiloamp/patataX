<?php
require_once('../vendor/autoload.php');
$client=GraphAware\Neo4j\Client\ClientBuilder::create()->addConnection('bolt', 'bolt://php:CodeAccess12@localhost:7687')->build();
$name = $password = $email = $username = null; 
$status = ['status' => 1, 'fields' => ['name' => 1, 'password' => 1, 'email' => 1, 'username' => 1]]; 
if($_SERVER['REQUEST_METHOD'] = 'POST'){
	if(isset($_POST['name']) && !empty($_POST['name'])){
		$name = $_POST['name'];
		$status['fields']['name'] = 0; 
	}
	else{
		$status['fields']['name'] = 2; 
	}
	if(isset($_POST['password']) && !empty($_POST['password'])){
		if(strlen($_POST['password']) >= 8){
			$password = $_POST['password'];
			$status['fields']['password'] = 0;
		}
		else{
			$status['fields']['password'] = 3; 
		} 
	}
	else{
		$status['fields']['password'] = 2; 
	}
	if(isset($_POST['email']) && !empty($_POST['email'])){ 
		if(filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)){
			$email = $_POST['email'];
			$status['fields']['email'] = 0;
		}
		else{
			$status['fields']['password'] = 3; 
		} 
	}
	else{
		$status['fields']['email'] = 2; 
	}
	if(isset($_POST['username']) && !empty($_POST['username'])){
		if(strlen($_POST['username']) <= 10){ 
			if(!preg_match( '/\s/',$_POST['username'])){
				$username = $_POST['username'];
				$status['fields']['username'] = 0; 
			}
			else{
				$status['fields']['username'] = 4;
			}
			
		}
		else{
			$status['fields']['username'] = 3;
		}
	}
	else{
		$status['fields']['username'] = 2; 
	}
	if($status['fields']['name'] === 0 && $status['fields']['username'] === 0 && $status['fields']['password'] === 0 && $status['fields']['email'] === 0){
		$status['status'] = 0;
		$password = password_hash($password, PASSWORD_BCRYPT, ['cost'=>11]);
		$client->run("CREATE (a:Account) SET a += {info}", ['info'=>['name'=>$name, 'username'=>$username,'email'=>$email,'password'=>$password]]);
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
