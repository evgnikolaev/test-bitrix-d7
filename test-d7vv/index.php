<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("Новый раздел");
?>

<? /*  --------------------------------    5 Компоненты на классах   ---------------------------------   */ ?>
<? $APPLICATION->IncludeComponent(
	"academy:class",
	"",
	Array(
		"CACHE_TIME" => "36000000",
		"CACHE_TYPE" => "A"
	)
); ?>

<? $APPLICATION->IncludeComponent(
	"academy:class.extends",
	"",
	Array(
		"CACHE_TIME" => "36000000",
		"CACHE_TYPE" => "A"
	)
); ?>
	<br><br><br>


<? /*  ------------------------  9 Отказ от работы с глобальными переменными  ---------------------   */ ?>
<?
//сокращенный вариант записи
//$context = \Bitrix\Main\Application::getInstance()->getContext();

//Получили объект приложения
$application = \Bitrix\Main\Application::getInstance();
//Полчили объект контекста
$context = $application->getContext();

//получаем объект, содержащий серверные данные
$server = $context->getServer();
echo 'путь до корня сайта - ' . $server->getDocumentRoot() . '<br>';
echo 'SERVER_NAME - ' . $server->getServerName() . '<br>';
echo 'HTTP_X_REAL_IP - ' . $server->get('HTTP_X_REAL_IP') . '<br>';

//Получаем объект, содержащий данные запроса
$request = $context->getRequest();
echo "request['name'] - " . $request['name'] . '<br>';
echo "request['name'] - " . $request->get('name') . '<br><br><br>';
?>


<? /*  ------------------------  11 Исключения  ---------------------   */ ?>
<? /*    Это не типичная ситуация, при которой нет смысла продолжать базовый алгоритм
        Например, если метод ожидает число, а мы передели строку. Метод не знает что делать со строкой.
		Выполнение страницы прерывается. Показ отладки в файле .settings.php
*/

//Выполнение страницы прерывается.
//throw new \Bitrix\Main\ArgumentTypeException('Аргумент который генерирует исключение','тип аргумента');

//Выполнение страницы не прервется, перехватим.
try {
	throw new \Bitrix\Main\ArgumentTypeException('Аргумент который генерирует исключение', 'тип аргумента');
} catch (Exception $e) {
	echo 'Перехваченное исключение';
	echo $e->getFile() . '<br>';
	echo $e->getRequiredType();
}
?>


<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>