<?php 

// File       : registerUser.php
// Description: Input File
// Last edit  : 30.01.2018 [Stanislav Koshevoy] - стандартизация и комментирование кода

require_once '../includes/DbOperations.php';

$response = array(); 

//Проверка на тип запроса
if($_SERVER['REQUEST_METHOD']=='POST'){
	//Проверка на входные параметры
	if(
		isset($_POST['username']) and 
			isset($_POST['email']) and 
				isset($_POST['password']))
		{
			
		$db = new DbOperations(); 

		//Отправка данных в createUser
		$result = $db->createUser( 	$_POST['username'],
									$_POST['password'],
									$_POST['email']
								);
								
		//Заполнение response.message на основании return $stmt->execute (значение $stmt описано в DbOperations.php->createUser)
		if($result == 1){
			$response['error'] = false; 
			$response['message'] = "Пользователь успешно зарегистрирован.";
		}elseif($result == 2){
			$response['error'] = true; 
			$response['message'] = "Произошла ошибка. Повторите попытку.";			
		}elseif($result == 0){
			$response['error'] = true; 
			$response['message'] = "Возможно, Вы уже зарегистрированы. Пожалуйста, выберите другой адрес электронной почты и имя пользователя";						
		}

	}else{
		$response['error'] = true; 
		$response['message'] = "Обязательные поля отсутствуют.";
	}
}else{
	$response['error'] = true; 
	$response['message'] = "Неверный запрос";
}

echo json_encode($response, JSON_UNESCAPED_UNICODE); //Параметр JSON_UNESCAPED_UNICODE, так как используем кириллицу.
