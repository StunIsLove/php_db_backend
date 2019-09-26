<?php 

// File       : feedUser.php
// Description: Input File
// Last edit  : 30.01.2018 [Stanislav Koshevoy] - файл создан

require_once '../includes/DbOperations.php';

$response = array(); 

//Проверка на тип запроса
if($_SERVER['REQUEST_METHOD']=='POST') {
	//Проверка на входные параметры
	if(isset($_POST['username'])){
		$db = new DbOperations(); 

		//Отправка данных в getFeed
		if($feed = $db->getFeed($_POST['username'])){
			$response['error'] = false;
			//Получение данных из таблицы feed
			$response['author'] = $feed['author'];
			$response['body'] = $feed['body'];
			$response['datetime'] = $feed['datetime'];
			$response['message'] = 'Лента новостей была обновлена.';
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