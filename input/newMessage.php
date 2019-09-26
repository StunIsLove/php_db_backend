<?php 

// File       : newMessage.php
// Description: Input File
// Last edit  : 04.02.2018 [Stanislav Koshevoy] - файл создан

require_once '../includes/DbOperations.php';

$response = array(); 

//Проверка на тип запроса
if($_SERVER['REQUEST_METHOD']=='POST'){
	//Проверка на входные параметры
	if(isset($_POST['username']) and 
			isset($_POST['contact'])
				isset($_POST['body']
					isset($_POST['datetime'])
		{
			
		$db = new DbOperations(); 

		//Отправка данных в newMessage
		$result = $db->newMessage($_POST['username'],
									$_POST['contact'],
									$_POST['body'],
									$_POST['datetime'],									
								);
								
		//Заполнение response.message на основании return $stmt->execute
		if($result == true){
			$response['error'] = false; 
			$response['message'] = "Сообщение успешно отправлено.";
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
