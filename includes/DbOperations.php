<?php 

// File       : DbConnect.php
// Description: Executable script
// Last edit  : 04.02.2018 [Stanislav Koshevoy] - создание класса отправки сообщений
	
	class DbOperations{

		private $con; 

		function __construct(){

			require_once dirname(__FILE__).'/DbConnect.php';

			$db = new DbConnect();

			$this->con = $db->connect(); //Создание подключения

		}

		//Класс создания пользователя (объявление переменных)
		public function createUser($username, $pass, $email){
			//Проверка на дублирование username & email
			if($this->isUserExist($username,$email)){
				return 0; 
			}else{
				$password = md5($pass); //Криптование пароля
				$stmt = $this->con->prepare("INSERT INTO `users` (`id`, `username`, `password`, `email`) VALUES (NULL, ?, ?, ?);"); //Тело запроса
				$stmt->bind_param("sss",$username,$password,$email); //Объявление переменных для запроса (s = string)

				//Возвращение данных (значения описаны в registerUser.php)
				if($stmt->execute()){
					return 1; 
				}else{
					return 2; 
				}
			}
		}

		//Класс авторизации
		public function userLogin($username, $pass){
			$password = md5($pass); //Криптование пароля
			$stmt = $this->con->prepare("SELECT id FROM users WHERE username = ? AND password = ?"); //Тело запроса
			$stmt->bind_param("ss",$username,$password); //Объявление переменных для запроса (s = string)
			$stmt->execute();
			$stmt->store_result(); 
			return $stmt->num_rows > 0; //Возвращение данных
		}

		//Класс возвращения данных в userLogin.php по имени пользователя
		public function getUserByUsername($username){
			$stmt = $this->con->prepare("SELECT * FROM users WHERE username = ?"); //Тело запроса
			$stmt->bind_param("s",$username); //Объявление переменных для запроса (s = string)
			$stmt->execute();
			return $stmt->get_result()->fetch_assoc(); //Возвращение данных
		}
		
		//Класс проверки имени пользователя и электронной почты на дублирование
		private function isUserExist($username, $email){
			$stmt = $this->con->prepare("SELECT id FROM users WHERE username = ? OR email = ?"); //Тело запроса
			$stmt->bind_param("ss", $username, $email); //Объявление переменных для запроса (s = string)
			$stmt->execute(); 
			$stmt->store_result(); 
			return $stmt->num_rows > 0; //Возвращение данных
		}
		
		//Класс получения данных для новостной ленты
		public function getFeed($username){
			$stmt = $this->con->prepare(SELECT contacts FROM users WHERE username = ?); //Тело запроса
			$stmt->bind_param("s", $username); //Объявление переменных для запроса (s = string)
			$stmt->execute();
			$stmt->bind_result($contacts); //Получение id контактов
			//Массив перебора id, для приведения данных в необходимый вид
			foreach ($contacts as list ($con_id)) {
				$stmt = $this->con->prepare(SELECT * FROM feed WHERE id = ?); //Тело запроса
				$stmt->bind_param("i", $con_id); //Объявление переменных для запроса
				$stmt->execute();
				return $stmt->get_result->fetch_assoc; //Получение и возвращение данных в feedUser.php
			}
		}
		
		//Класс получения списка контактов
		public function getContactList($username){
			$stmt = $this->con->prepare (SELECT contacts FROM users WHERE username = ?); //Тело запроса
			$stmt->bind_param("s", $username); //Объявление переменных для запроса
			$stmt->execute();
			$stmt->bind_result(contacts); //Получение id контактов
			//Массив перебора id, для приведения данных в необходимый вид
			foreach ($contacts as list ($con_id)) {
				$stmt = $this->con->prepare(SELECT * FROM users WHERE id = ?); //Тело запроса
				$stmt->bind_param("i", $con_id); //Объявление переменных для запроса
				$stmt->execute();
				return $stmt->get_result->fetch_assoc; //Получение и возвращение данных в contactList.php
			}
		}
		
		//Класс получения списка конференций
		public function getGroupsList($username){
			$stmt = $this->con->prepare (SELECT groups FROM users WHERE username = ?); //Тело запроса
			$stmt->bind_param("s", $username); //Объявление переменных для запроса
			$stmt->execute();
			$stmt->bind_result(groups); //Получение id конференций
			//Массив перебора id, для приведения данных в необходимый вид
			foreach ($groups as list ($grp_id)) {
				$stmt = $this->con->prepare(SELECT * FROM users WHERE id = ?); //Тело запроса
				$stmt->bind_param("i", $grp_id); //Объявление переменных для запроса
				$stmt->execute();
				//Проверка на наличие групп
				if($stmt->num_rows > 0){
					//Если группы найдены, то идём забиарать members, posts и meets
					foreach ($posts as list ($post_id)) {
						$stmt = $this->con->prepare(SELECT author, body, datetime FROM posts WHERE id = ?); //Тело запроса
						$stmt->bind_param("i", $post_id); //Объявление переменных для запроса
						$stmt->execute();
						$posts = $stmt->bind_result($author, $body, $datetime);
					}
					foreach ($meets as list ($meet_id)) {
						$stmt = $this->con->prepare(SELECT author, title, datetime FROM meets WHERE id = ?); //Тело запроса
						$stmt->bind_param("i", $meet_id); //Объявление переменных для запроса
						$stmt->execute();
						$meets = $stmt->bind_result($author, $title, $datetime);
					}
					foreach ($members as list ($user_id)) {
						$stmt = $this->con->prepare(SELECT username FROM users WHERE id = ?); //Тело запроса
						$stmt->bind_param("i", $user_id); //Объявление переменных для запроса
						$stmt->execute();
						$members = $stmt->bind_result($username);
					}
					return $stmt->get_result->fetch_assoc; //Возвращение данных до проверки в userGroups.php
					return ($posts, $meets, $members);
				} else { 
					return "Вы не состоите в группах"; //Возвращение сообщения в случае ошибки
				}
			}
		}
		
		//Класс получения списка личных сообщений
		public function getMessagesList($username){
			$stmt = $this->con->prepare (SELECT messages FROM users WHERE username = ?); //Тело запроса
			$stmt->bind_param("s", $username); //Объявление переменных для запроса
			$stmt->execute();
			$stmt->bind_result(messages); //Получение id сообщения
			//Массив перебора id, для приведения данных в необходимый вид
			foreach ($messages as list ($mes_id)) {
				$stmt = $this->con->prepare(SELECT * FROM messages WHERE id = ?); //Тело запроса
				$stmt->bind_param("i", $mes_id); //Объявление переменных для запроса
				$stmt->execute();
				//Проверка на наличие сообщений
				if($stmt->num_rows > 0){
					//Если сообщения найдены, то идём забирать username и posts
					foreach ($users as list ($user_id)) {
						$stmt = $this->con->prepare(SELECT username FROM users WHERE id = ?); //Тело запроса
						$stmt->bind_param("i", $user_id); //Объявление переменных для запроса
						$stmt->execute();
						$users = $stmt->bind_result($username);
					}
					foreach ($posts as list ($post_id)) {
						$stmt = $this->con->prepare(SELECT author, body, datetime FROM posts WHERE id = ?); //Тело запроса
						$stmt->bind_param("i", $post_id); //Объявление переменных для запроса
						$stmt->execute();
						$posts = $stmt->bind_result($author, $body, $datetime);
					}
					return $stmt->get_result->fetch_assoc; //Возвращение данных до проверки в userMessages.php
					return ($posts, $users);
				} else { 
					return "У Вас нет сообщений."; //Возвращение сообщения в случае ошибки
				}
			}
		}
		
		//Класс получения списка встреч
		public function getMeetsList($username){
			$stmt = $this->con->prepare (SELECT meets FROM users WHERE username = ?); //Тело запроса
			$stmt->bind_param("s", $username); //Объявление переменных для запроса
			$stmt->execute();
			$stmt->bind_result($meets); //Получение id встречи
			//Массив перебора id, для приведения данных в необходимый вид
			foreach ($meets as list ($meet_id)) {
				$stmt = $this->con->prepare(SELECT * FROM meets WHERE id = ?); //Тело запроса
				$stmt->bind_param("i", $meet_id); //Объявление переменных для запроса
				$stmt->execute();
				//Проверка на наличие встреч
				if($stmt->num_rows > 0){
					return $stmt->get_result->fetch_assoc; //Возвращение данных до проверки в userMeets.php
				} else { 
					return "На данный момент нет доступных встреч."; //Возвращение сообщения в случае ошибки
				}
			}
		}
		
		//Класс создания макреа (объявление переменных)
		public function createMarker($name, $latlng){
			//Проверка на дублирование названия и координат
			if($this->isMarkerExist($name, $latlng)){
				return 0; 
			}else{
				$stmt = $this->con->prepare("INSERT INTO `markers` (`id`, `name`, `latlng`) VALUES (NULL, ?, ?);"); //Тело запроса
				$stmt->bind_param("ss",$name,$latlng); //Объявление переменных для запроса (s = string)

				//Возвращение данных (значения описаны в markersUser.php)
				if($stmt->execute()){
					return 1; 
				}else{
					return 2; 
				}
			}
		}
		
		//Класс проверки маркера на дублирование
		private function isMarkerExist($name, $latlng){
			$stmt = $this->con->prepare("SELECT id FROM markers WHERE name = ? OR latlng = ?"); //Тело запроса
			$stmt->bind_param("ss", $name, $latlng); //Объявление переменных для запроса (s = string)
			$stmt->execute(); 
			$stmt->store_result(); 
			return $stmt->num_rows > 0; //Возвращение данных
		}
		
		//Класс создания новости
		public function createNews($username, $body, $datetime){
			$stmt = $this->con->prepare("INSERT INTO `feed` (`id`, `author`, `body`, `datetime`) VALUES (NULL, ?, ?, ?);");
			$stmt->bind_param("sss", $username, $body, $datetime); //Объявление переменных для запроса (s = string)
			$stmt->execute();
			if($stmt->execute()){
				return true; 
			}else{
				return false; 
			}
		}
		
		//Класс добавления контакта через ID
		public function newContactId($username, $id){
			$stmt = $this->con->prepare("SELECT contacts FROM users WHERE username = ?"); //Тело запроса
			$stmt->bind_param("s", $username); //Объявление переменных для запроса (s = string)
			$stmt->execute();
			$stmt->bind_result($contacts);
			$newConact = ($contacts.", ".$id);
			$stmt = $this->con->prepare("UPDATE `users` SET `contacts` = ? WHERE `username` = ?");
			$stmt->bind_param("ss", $newConact, $username); //Объявление переменных для запроса (s = string)
			if($stmt->execute()){
				return true; 
			}else{
				return false; 
			}
		}
		
		//Класс добавления контакта через E-Mail
		public function newContactEmail($username, $email){
			$stmt = $this->con->prepare("SELECT contacts FROM users WHERE username = ?"); //Тело запроса
			$stmt->bind_param("s", $username); //Объявление переменных для запроса (s = string)
			$stmt->execute();
			$stmt->bind_result($contacts);
			$stmt = $this->con->prepare("SELECT id FROM users WHERE email = ?"); //Тело запроса
			$stmt->bind_param("s", $email); //Объявление переменных для запроса (s = string)
			$stmt->execute();
			$stmt->bind_result($id);
			$newConact = ($contacts.", ".$id);
			$stmt = $this->con->prepare("UPDATE `users` SET `contacts` = ? WHERE `username` = ?");
			$stmt->bind_param("ss", $newConact, $username); //Объявление переменных для запроса (s = string)
			if($stmt->execute()){
				return true; 
			}else{
				return false; 
			}
		}
		
		//Класс добавления контакта через имя пользователя
		public function newContactName($username, $contactname){
			$stmt = $this->con->prepare("SELECT contacts FROM users WHERE username = ?"); //Тело запроса
			$stmt->bind_param("s", $username); //Объявление переменных для запроса (s = string)
			$stmt->execute();
			$stmt->bind_result($contacts);
			$stmt = $this->con->prepare("SELECT id FROM users WHERE username = ?"); //Тело запроса
			$stmt->bind_param("s", $contactname); //Объявление переменных для запроса (s = string)
			$stmt->execute();
			$stmt->bind_result($id);
			$newConact = ($contacts.", ".$id);
			$stmt = $this->con->prepare("UPDATE `users` SET `contacts` = ? WHERE `username` = ?");
			$stmt->bind_param("ss", $newConact, $username); //Объявление переменных для запроса (s = string)
			if($stmt->execute()){
				return true; 
			}else{
				return false; 
			}
		}
		
		//Класс создания группы
		public function newGroup($groupname, $members, $username){
			$stmt = $this->con->prepare("INSERT INTO `groups` (`id`, `name`, `members`, `admin`) VALUES (NULL, ?, ?, ?);");
			$stmt->bind_param("sss", $groupname, $members, $username); //Объявление переменных для запроса (s = string)
			$stmt->execute();
			if($stmt->execute()){
				return true; 
			}else{
				return false; 
			}
		}
		
		//Класс создания встречи
		public function newMeet($username, $latlng, $title, $datetime){
			$stmt = $this->con->prepare("INSERT INTO `meets` (`id`, `author`, `latlng`, `title`, `datetime`) VALUES (NULL, ?, ?, ?, ?);");
			$stmt->bind_param("ssss", $username, $latlng, $title, $datetime); //Объявление переменных для запроса (s = string)
			$stmt->execute();
			if($stmt->execute()){
				return true; 
			}else{
				return false; 
			}
		}
		
		//Класс отправки сообщения
		public function newMessage($username, $contact, $post, $datetime){
			//Создание записи в таблице posts для дальнейшего использования
			$stmt = $this->con_prepare("INSERT INTO `posts` (`id`, `author`, `body`, `datetime`) VALUES (NULL, ?, ?, ?);"); 
			$stmt->bind_param("sss", $username, $post, $datetime);
			$stmt->execute();
			$stmt = $this->con->prepare("SELECT id FROM posts WHERE body = ?"); //Получаем id созданной записи
			$stmt->bind_param("s", $post_id);
			$stmt->execute();
			$stmt->bind_result($id);
			$users = ($username.", ".$contact); //Приводим данные в необходимый формат
			//Отпарвляем готовую информацию в таблицу messages
			$stmt = $this->con_prepare("INSERT INTO `messages` (`id`, `users`, `posts`, `datetime`) VALUES (NULL, ?, ?, ?);");
			$stmt->bind_param("sss", $users, $post_id, $datetime);
			//Возвращаем true or false
			if($stmt->execute()){
				return true; 
			}else{
				return false; 
			}			
		}

	}