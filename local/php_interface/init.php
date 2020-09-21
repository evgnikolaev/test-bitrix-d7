<?php

//        ОБРАБОТЧИК ORM НА ДОБАВЛЕНИЕ
//  Старый способ, здесь в callback будет приходить массив
//  \Bitrix\Main\EventManager::getInstance()->addEventHandlerCompatible('academy.d7', '\Academy\D7\Book\::OnBeforeAdd', ['MyClass', 'MyOrmEvent']);

//  новый способ, в callback приходит объект  - MyOrmEvent(\Bitrix\Main\Entity\Event $event)
// используем класс Book , не BookTable
\Bitrix\Main\EventManager::getInstance()->addEventHandler('academy.d7', '\Academy\D7\Book::OnBeforeAdd', ['MyClass', 'MyOrmEvent']);


class MyClass
{
	static function MyOrmEvent(\Bitrix\Main\Entity\Event $event)
	{
		$fields = $event->getParameter('fields');
		echo "Событие на добавление (из init.php) : ";
		echo "<pre>";
		var_dump($fields);
		echo "</pre>";
		echo '------------------<br><br>';

	}
}