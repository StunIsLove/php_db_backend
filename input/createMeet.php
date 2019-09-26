<?php 

// File       : createMeet.php
// Description: Input File
// Last edit  : 31.01.2018 [Stanislav Koshevoy] - файл создан

require_once '../includes/DbOperations.php';

$response = array(); 

//Проверка на тип запроса
if($_SERVER['REQUEST_METHOD']=='POST'){
	//Проверка на входные параметры
	if(
		isset($_POST['username']) and 
			isset($_POST['latlng'])
				isset($_POST['title']
					isset($_POST['datetime'])
		{
			
		$db = new DbOperations(); 

		//Отправка данных в createMeet
		$result = $db->creatMeet($_POST['username'],
									$_POST['latlng'],
									$_POST['title'],
									$_POST['datetime'],									
								);
								
		//Заполнение response.message на основании return $stmt->execute
		if($result == true){
			$response['error'] = false; 
			$response['message'] = "Встреча успешно опубликована.";
		}else{
			$response['error'] = true; 
			$response['message'] = "Произошла ошибка. Повторите попытку позже.";			
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
