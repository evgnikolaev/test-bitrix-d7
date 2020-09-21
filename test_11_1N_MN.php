<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("Новая страница");

use Academy\D7\Book_1nTable;
use Academy\D7\Author_1nTable;

\Bitrix\Main\Loader::includeModule('academy.d7');





//------------------------    связь одна книга - один автор    ---------------------------------
$result = Book_1nTable::getList([
	'select' => ['NAME', 'AUTHOR.NAME', 'AUTHOR.LAST_NAME']
]);
echo "<pre>";
print_r($result->fetchAll());
echo "</pre>";






//------------------------    связь один автор - много книг (обратный референс)    ---------------------------------
$result = Author_1nTable::getList([
	'select' => [
		'NAME',
		'LAST_NAME',

		//   описываем обратный референс
		//   \Academy\D7\Book2Table - полное имя класса с неймспейс связанной сущности
		//   AUTHOR   -  имя поля в связанной сущности  (в которой хранится связь с текущей сущностью)
		//   NAME -  имя поля той сущности, которой мы хотим получить
		//   !!! Все части имеют отношение к сущности книги !!!
		'\Academy\D7\Book_1nTable:AUTHOR.NAME'
	]
]);
echo "<pre>";
print_r($result->fetchAll());
echo "</pre>";





//------------------------    связь один автор - много книг (обратный референс)    ---------------------------------
$result = \Academy\D7\Book_mnTable::getList([
	'select' => [
		'NAME',
		//  имя автора, синтаксис подобен 1:N
		//   Academy\D7\BookAuthorsUs_mnTable- полное имя класса с неймспейс связанной сущности
		//   BOOK   -  имя поля в связанной сущности  (в которой хранится связь с текущей сущностью)
		//   AUTHOR   -  имя поля в которой хранится связь с сущностья автора
		//   NAME -  имя поля той сущности, которой мы хотим получить
		//   !!! Все части имеют отношение к сущности книги !!!
		'AUTHOR_NAME'=>'\Academy\D7\BookAuthorsUs_mnTable:BOOK.AUTHOR.NAME'

	],
	'data_doubling'=>false
]);

echo "<pre>";
print_r($result->fetchAll());
echo "</pre>";

?>
<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>