<?php 

// File       : newContact.php
// Description: Input File
// Last edit  : 31.01.2018 [Stanislav Koshevoy] - файл создан

require_once '../includes/DbOperations.php';

$response = array(); 

//Проверка на тип запроса
if($_SERVER['REQUEST_METHOD']=='POST'){
	//Проверка на входные параметры
	if(isset($_POST['username']) and 
			isset($_POST['id']))
		{
		//Если выбрано добавление по ID
		$db = new DbOperations(); 

		//Отправка данных в newContact
		$result = $db->newContactId($_POST['username'],
									$_POST['id']);
								
		//Заполнение response.message на основании return $stmt->execute
		if($result == true){
			$response['error'] = false; 
			$response['message'] = "Пользователь добавлен в список контактов.";
		}elseif($result == false){
			$response['error'] = true; 
			$response['message'] = "Произошла ошибка. Повторите попытку позже.";			
		}elseif(isset($_POST['username']) and 
			isset($_POST['email'])){
				//Если выбрано добавление по E-Mail
				$db = new DbOperations(); 

				//Отправка данных в newContact
				$result = $db->newContactEmail($_POST['username'],
									$_POST['email']);
								
				//Заполнение response.message на основании return $stmt->execute
				if($result == true){
					$response['error'] = false; 
					$response['message'] = "Пользователь добавлен в список контактов.";
				}elseif($result == false){
					$response['error'] = true; 
					$response['message'] = "Произошла ошибка. Повторите попытку позже.";			
				}
			}elseif(isset($_POST['username']) and 
			isset($_POST['contactname'])){
				//Если выбрано добавление по имени пользователя
				$db = new DbOperations(); 

				//Отправка данных в newContact
				$result = $db->newContactName($_POST['username'],
									$_POST['contactname']);
								
				//Заполнение response.message на основании return $stmt->execute
				if($result == true){
					$response['error'] = false; 
					$response['message'] = "Пользователь добавлен в список контактов.";
				}elseif($result == false){
					$response['error'] = true; 
					$response['message'] = "Проверьте правильность вводимых данных.";			
				}
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
