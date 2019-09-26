<?php 

// File       : userLogin.php
// Description: Input File
// Last edit  : 30.01.2018 [Stanislav Koshevoy] - стандартизация и комментирование кода

require_once '../includes/DbOperations.php';

$response = array(); 

//Проверка на тип запроса
if($_SERVER['REQUEST_METHOD']=='POST') {
	//Проверка на входные параметры
	if(isset($_POST['username']) and isset($_POST['password'])){
		$db = new DbOperations(); 

		//Отправка данных в userLogin, проверка username & password на правильность
		if($db->userLogin($_POST['username'], $_POST['password'])){
			//Отправка username в getUserByUsername
			$user = $db->getUserByUsername($_POST['username']);
			$response['error'] = false; 
			//Получение данных пользователя из get_result
			$response['id'] = $user['id'];
			$response['email'] = $user['email'];
			$response['username'] = $user['username'];
		}else{
			$response['error'] = true; 
			$response['message'] = "Неправильное имя пользователя или пароль.";			
		}

	}else{
		$response['error'] = true; 
		$response['message'] = "Обязательные поля отсутствуют.";
	}
}

echo json_encode($response, JSON_UNESCAPED_UNICODE); //Параметр JSON_UNESCAPED_UNICODE, так как используем кириллицу.