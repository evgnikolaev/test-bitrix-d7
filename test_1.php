<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("Новая страница");

use Academy\D7\BookTable;

\Bitrix\Main\Loader::includeModule('academy.d7');


// !!!  Обновление, удаление, изменение для нескольких элементов нужно делать в цикле  !!!



//------------------------  Добавление  ----------------------------
$arResult = '';
$result = BookTable::add([
	'NAME'          => 'Книга для теста',
	'RELEASED'      => '2002',
	'ISBN'          => '001-21111',
	'AUTHOR'        => 'Сергей Покоев',
	'TIME_INTERVAL' => new Bitrix\Main\Type\DateTime('04.09.2015 00:00:00'),
	'DESCRIPTION'   => 'текст1
								текст2',
]);

if ($result->isSuccess()) {
	$id = $result->getId();
	$arResult = 'Запись добавлена с id' . $id;
} else {
	$error = $result->getErrorMessages();
	$arResult = 'Произошла ошибка при добавлении : <pre> ' . var_dump($error, true) . '</pre>';
}

echo $arResult;


//------------------------  Изменение  ----------------------------
$arResult = '';
//BookTable::update( 'id', 'указываем только те поля, которые мы изменяем' );
$result = BookTable::update(3, [
	'NAME' => 'книга изменненная',
	'ISBN' => 'aasd'
]);

if ($result->isSuccess()) {
	$id = $result->getId();
	$arResult = 'Запись изменена с id' . $id;
} else {
	$error = $result->getErrorMessages();
	$arResult = 'Произошла ошибка при добавлении : <pre> ' . var_dump($error, true) . '</pre>';
}

echo $arResult;


//------------------------  Удаление  ----------------------------
$arResult = '';
//BookTable::delete( 'id' );
// удаление несуществующего элемента в mysql не является ошибкой. Поэтому удаление несуществующего элемента не будет ошибкой.
$result = BookTable::delete(1);

if ($result->isSuccess()) {
	//$id = $result->getId();// при удалении этот метод уже не применим (нельзя узнать id удаленной записи)
	$arResult = 'Запись удалена';
} else {
	$error = $result->getErrorMessages();
	$arResult = 'Произошла ошибка при добавлении : <pre> ' . var_dump($error, true) . '</pre>';
}

echo $arResult;


//------------------------  Вычисляемое поле  ----------------------------
$arResult = '';
//  new Bitrix\Main\DB\SqlExpression('?# + 1','WRITE_COUNTER')
//  Вычисляемые значения - используются когда берем значение, неважно какое оно, произвести с ним действия и положить обратно.
//       Плейсхолдеры:
//            ? или ?s - Значение экранируются и заключаются в кавычки '
//            ?# - значение экранируется как идентификатор
//            ?i - значение приводится к integer
//            ?f - значение приводится к float

$result = BookTable::update(2, [
	'NAME'        => 'книга изменненная',
	'WRITE_COUNT' => new Bitrix\Main\DB\SqlExpression('?# + 1', 'WRITE_COUNT')
]);

if ($result->isSuccess()) {
	$id = $result->getId();
	$arResult = 'Запись изменена с id' . $id;
} else {
	$error = $result->getErrorMessages();
	$arResult = 'Произошла ошибка при добавлении : <pre> ' . var_dump($error, true) . '</pre>';
}

echo $arResult;


//------------------------  getlist ----------------------------

//  Если раньше имел разные параметры, то сейчас единый.
//  Если вызвать getList без параметров, то будут выбраны все элементы сущности, все поля кроме вычисляемых, без сортировок и ограничений
$result = BookTable::getList([
	//Имена полей с алиасами.
	// Если не указать select - будут выбраны все поля scalarField, а поля expressionField и отношения с другими сущностями выраны не будут. Их нужно указывать явно.
	'select' => ['ID', 'NAME_BOOK' => 'NAME', 'AGE_YEAR', 'WRITE_COUNT'],
	'filter' => ['WRITE_COUNT' => 0], //Описание для фильтра WHERE и HAVING (может быть сложным при помощи AND OR)
	//'group'  => [], // поля для группировки (явное указание полей, по которым нужно группировать результат)
	'order'  => ['ID' => 'ASC'], //сортировка
	// limit и offset помогают ограничить количество выбираемых записей и реализовать постраничную выборку
	//выбираем 3 записи, начиная с 3-ей(пропустив 2 первые)
	'limit'  => 3, // количество записей
	'offset' => 2 // смещение для limit
]);

//getlist возвращает объект DbResult

//1 способ
$arResult = [];
while ($row = $result->fetch()) {
	$arResult[] = $row;
}

//2 способ
//$arResult = $result->fetchAll();




//------------------------  getlist runTime----------------------------

//   --1)
//    У getlist есть еще параметр  runtime  - Динамически созданное поле ExpressionField.
//    ExpressionField поля чаще нужны не в описании сущности (getMap), сколько при различных выборках с группировкой.
//    Например один из вариантов применения - подсчет количества элементов (его не нужно описывать в описании сущности getMap)
$result = BookTable::getList([
	'select'  => ['CNT'],
	'runtime' => [new \Bitrix\Main\Entity\ExpressionField('CNT', 'COUNT(*)')] // имя поля, sql выражение
	// после мы можем ссылаться на это поле CNT в фильтре, группировке и других
]);
$arResult = $result->fetchAll();


//   Если runtime нужен только в select (чаще всего бывает), можно его поместить сразу в select
//          $result = BookTable::getList([
//	            'select'  => [new \Bitrix\Main\Entity\ExpressionField('CNT', 'COUNT(*)')],
//          ]);
//          $arResult = $result->fetchAll();



//   --2)
//      Использование runtime для добавления  полей других типов
//	    В runtime можно регистрировать не только EspressionFields, но и поля любых других типов
//      !!  Механизм runtime работает таким образом, что к сущности добавляется новое поле, будто оно было описано в ней изначально в методе getMap !!
//          Пример:
//              Мы в таблице БД добавили новое поле ACTIVITY, но не описали ее в getMap.
//              в runtime объявим это поле при помощи new \Bitrix\Main\Entity\IntegerField('ACTIVITY'),
//      Особенностью поля runtime является то что он виден только в рамках одного запроса. При следующем вызове такое поле уже будет недоступно.
//      Придется заново его зарегистрировать. Поэтому причины использования такого поля могут быть:
//      - Вы используете ORM-сущность созданную не вами, и править его не можете. А с помощъю runtime вы дополняете ORM-сущность.
//      - Ваше поле будет использоваться только в одном запросе, и смысла в рамках других не имеет.
//      - Описанное поле не имеет смысла описывать в описании сущности (EspressionFields).


$result = BookTable::getList([
	'select'  => ['ID', 'NAME', 'ACTIVITY'],
	'filter'  => ['ACTIVITY' => 1],
	'runtime' => [new \Bitrix\Main\Entity\IntegerField('ACTIVITY')]
]);
$arResult = $result->fetchAll();



//------------------------  Использование query  ----------------------------
//    удобно использовать, когда парметры собираются в разных местах, когда идет большая подготовительная работа перед запросом !!!
//    Все методы - https://take.ms/9KlpA !!

$arResult = '';
$q = new \Bitrix\Main\Entity\Query(BookTable::getEntity());

//имена полей, которые необходимо получить в результате
$q->setSelect(['ID', 'NAME_BOOK' => 'NAME', 'AGE_YEAR', 'WRITE_COUNT']);

//описание фильра для WHERE и HAVING
$q->setFilter(['WRITE_COUNT'=>0]);

//параметры сортировки
$q->setOrder(['ID'=>'DESC']);

//количество записей
$q->setLimit(3);

//смещение для limit
$q->setOffset(2);

$result = $q->exec();

$arResult = $result->fetchAll();


?>
<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>