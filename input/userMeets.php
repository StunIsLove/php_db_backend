<?php 

// File       : userMeets.php
// Description: Input File
// Last edit  : 31.01.2018 [Stanislav Koshevoy] - файл создан

require_once '../includes/DbOperations.php';

$response = array(); 

//Проверка на тип запроса
if($_SERVER['REQUEST_METHOD']=='POST') {
	//Проверка на входные параметры
	if(isset($_POST['username'])){
		$db = new DbOperations(); 

		//Отправка данных в getMeetsList
		if($list = $db->getMeetsList($_POST['username'])){
			$response['error'] = false;
			//Получение данных из таблицы meets
			$response['id'] = $list['id'];
			$response['author'] = $list['author'];
			$response['title'] = $list['title'];
			$response['body'] = $list['snippet'];
			$response['datetime'] = $list['datetime'];
		}else{
			$response['error'] = true; 
			$response['message'] = "Пожалуйста, авторизуйтесь заново.";			
		}

	}else{
		$response['error'] = true; 
		$response['message'] = "Не удается получить параметр username.";
	}
}

echo json_encode($response, JSON_UNESCAPED_UNICODE); //Параметр JSON_UNESCAPED_UNICODE, так как используем кириллицу.