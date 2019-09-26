<?php 

// File       : markersUser.php
// Description: Input File
// Last edit  : 31.01.2018 [Stanislav Koshevoy] - файл создан

require_once '../includes/DbOperations.php';

$response = array(); 

//Проверка на тип запроса
if($_SERVER['REQUEST_METHOD']=='POST'){
	//Проверка на входные параметры
	if(
		isset($_POST['name']) and 
			isset($_POST['latlng']))
		{
			
		$db = new DbOperations(); 

		//Отправка данных в createMarker
		$result = $db->createMarker($_POST['name'],
									$_POST['latlng']
								);
								
		//Заполнение response.message на основании return $stmt->execute (значение $stmt описано в DbOperations.php->createMarker)
		if($result == 1){
			$response['error'] = false; 
			$response['message'] = "Маркер успешно добавлен.";
		}elseif($result == 2){
			$response['error'] = true; 
			$response['message'] = "Произошла ошибка. Повторите попытку позже.";			
		}elseif($result == 0){
			$response['error'] = true; 
			$response['message'] = "Возможно, маркер уже был создан. Пожалуйста, выберите другое название и координаты";						
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
