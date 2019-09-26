<?php 

// File       : DbConnect.php
// Description: MySQL connection
// Last edit  : 30.01.2018 [Stanislav Koshevoy] - комментирование кода

	class DbConnect{

		private $con; 

		function __construct(){

		}

		function connect(){
			include_once dirname(__FILE__).'/Constants.php'; //Используем постоянные
			$this->con = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME); //Создание нового MySQL подключения

			if(mysqli_connect_errno()){
				echo "Не удалось подключиться к базе данных."; //Проверка на ошибки.
			}

			return $this->con; 
		}
	}