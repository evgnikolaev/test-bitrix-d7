<?

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Application;
use \Bitrix\Main\Entity\Base;

Loc::loadMessages(__FILE__);

// academy_d7 класс должен быть такой же как id модуля . точку заменяем на подчеркивание
// языковые константы начинаем с id модуля
class academy_d7 extends CModule
{
	var $exclusionAdminFiles;

	function __construct()
	{
		$arModuleVersion = [];
		include(__DIR__ . "/version.php");

		$this->exclusionAdminFiles = [
			'..',
			'.',
			'menu.php',
			'operation_description.php',
			'task_description.php',
		];

		// Заполняем переменные модуля
		$this->MODULE_ID = 'academy.d7';
		$this->MODULE_VERSION = $arModuleVersion['VERSION'];
		$this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];

		$this->MODULE_NAME = Loc::getMessage('ACADEMY_D7_MODULE_NAME');
		$this->MODULE_DESCRIPTION = Loc::getMessage('ACADEMY_D7_MODULE_DESCRIPTION');

		$this->PARTNER_NAME = Loc::getMessage('ACADEMY_D7_PARTNER_NAME');
		$this->PARTNER_URI = Loc::getMessage('ACADEMY_D7_PARTNER_URI');

		$this->MODULE_SORT = 1;

		//Y - будет показываться группа администраторов в списке групп на странице настроек модуля на вкладеке доступ (ее нужно еще запрограммировать)
		$this->SHOW_SUPER_ADMIN_GROUP_RIGHTS = 'Y';

		//модуль будет показываться на странице редактирвания "настройки - пользователи - группы пользователей" на вкладке доступы.
		$this->MODULE_GROUP_RIGHTS = 'Y';
	}

	function DoInstall()
	{
		global $APPLICATION;

		//дополнительно можно проверить,если наш модлуь зависит от других модулей: 	\Bitrix\Main\ModuleManager::isModuleInstalled(MODULE_ID)
		if ($this->isVersionD7()) {

			//запись, что модуль установлен
			\Bitrix\Main\ModuleManager::registerModule($this->MODULE_ID);

			//обычно создаются таблицы и заполняются демо данными
			$this->InstallDB();

			// регистрируем обработчики
			$this->InstallEvents();

			// файлы, как правило копируем  компоненты, шаблоны, своя админка
			$this->InstallFiles();

			//работа с settings.php
			$configduration = \Bitrix\Main\Config\Configuration::getInstance();
			$academy_module_d7 = $configduration->get('academy_module_d7');
			$academy_module_d7['install'] = $academy_module_d7['install'] + 1;
			$configduration->add('academy_module_d7', $academy_module_d7);
			$configduration->saveConfiguration();
			//работа с settings.php

		} else {
			$APPLICATION->ThrowException(Loc::getMessage('ACADEMY_D7_INSTALL_ERROR_VERSION'));
		}

		//задаем шаг
		$APPLICATION->IncludeAdminFile(Loc::getMessage('ACADEMY_D7_INSTALL_TITLE'), $this->GetPath() . '/install/step.php');

	}


	// проделываем обратные операции DoInstall
	function DoUninstall()
	{
		global $APPLICATION;
		$context = Application::getInstance()->getContext();
		$request = $context->getRequest();


		if ($request['step'] < 2) {
			$APPLICATION->IncludeAdminFile(Loc::getMessage('ACADEMY_D7_UNINSTALL_TITLE'), $this->GetPath() . '/install/unstep1.php');
		} elseif ($request['step'] == 2) {
			$this->UnInstallEvents();
			$this->UnInstallFiles();

			if ($request['savedata'] != 'Y') {
				$this->UnInstallDB();
			}
			\Bitrix\Main\ModuleManager::unRegisterModule($this->MODULE_ID);


			//работа с settings.php
			$configduration = \Bitrix\Main\Config\Configuration::getInstance();
			$academy_module_d7 = $configduration->get('academy_module_d7');
			$academy_module_d7['uninstall'] = $academy_module_d7['uninstall'] + 1;
			$configduration->add('academy_module_d7', $academy_module_d7);
			$configduration->saveConfiguration();
			//работа с settings.php


			$APPLICATION->IncludeAdminFile(Loc::getMessage('ACADEMY_D7_UNINSTALL_TITLE'), $this->GetPath() . '/install/unstep2.php');
		}
	}


	//Проверяем, что система поддерживает d7
	public function isVersionD7()
	{
		return CheckVersion(\Bitrix\Main\ModuleManager::getVersion('main'), '14.00.00');
	}


	//место определения модуля (чтобы работал и в bitrix и в local)
	public function GetPath($notDocumentRoot = false)
	{
		if ($notDocumentRoot) {
			return str_ireplace(Application::getDocumentRoot(), '', dirname(__DIR__));
		} else {
			return dirname(__DIR__);
		}
	}


	function InstallFiles($arParams = [])
	{
		//копируем компоненты
		$path = $this->GetPath() . '/install/components';
		if (\Bitrix\Main\IO\Directory::isDirectoryExists($path)) {
			CopyDirFiles($path, $_SERVER['DOCUMENT_ROOT'] . '/bitrix/components', true, true);
		} else {
			//выбрасываем исключение, некорректный путь
			throw new \Bitrix\Main\IO\InvalidPathException($path);
		}


		// кпируем административные скрипты
		$path = $this->GetPath() . '/admin/';
		if (\Bitrix\Main\IO\Directory::isDirectoryExists($path)) {
			// 1 способ (5 урок ч2)
			CopyDirFiles($this->GetPath() . '/install/admin', $_SERVER['DOCUMENT_ROOT'] . '/bitrix/admin');
			// 2 способ  (5 урок ч2)
			if ($dir = opendir($path)) {
				while (false !== $item = readdir($dir)) {
					if (in_array($item, $this->exclusionAdminFiles)) {
						continue;
					}

					file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/bitrix/admin' . $this->MODULE_ID . '_' . $item,
						'<' . '? require $_SERVER["DOCUMENT_ROOT"]."' . $this->GetPath(true) . '/admin/' . $item . '");?' . '>');
				}
			}
		}
	}


	function UnInstallFiles()
	{
		\Bitrix\Main\IO\Directory::deleteDirectory($_SERVER['DOCUMENT_ROOT'] . '/bitrix/components/academy');


		if (\Bitrix\Main\IO\Directory::isDirectoryExists($path = $this->GetPath() . '/admin/')) {
			DeleteDirFiles($_SERVER['DOCUMENT_ROOT'] . $this->GetPath() . '/install/admin', $_SERVER['DOCUMENT_ROOT'] . '/bitrix/admin');
			if ($dir = opendir($path)) {
				while (false !== $item = readdir($dir)) {
					if (in_array($item, $this->exclusionAdminFiles)) {
						continue;
					}

					\Bitrix\Main\IO\File::deleteFile($_SERVER['DOCUMENT_ROOT'] . '/bitrix/admin' . $this->MODULE_ID . '_' . $item);
				}
				closedir($dir);
			}
		}
	}


	/*
	 6) взаимодействия модулей. 2 способа:
	- Использование API стороннего модуля. сперва его нужно подключить.
	- События
	!!! Работа с событиями на уровне модуля не предусматривает использования init.php !!!!
	111 Регистрация обработчика должна производится один раз при установке, при удалении модуля эту регистрацию нужно снимать !!!
	!!! Если не опишем события, то при взаимодействии модулей не сможем настроить это взаимодействие !!!
	Дополнительно 17 урок.
	*/
	function InstallEvents()
	{
//		\Bitrix\Main\EventManager::getInstance()->registerEventHandler($this->MODULE_ID, 'TestD7', $this->MODULE_ID, '\Academy\D7\Event', 'eventHandler');
	}

	function UnInstallEvents()
	{
//		\Bitrix\Main\EventManager::getInstance()->unRegisterEventHandler($this->MODULE_ID, 'TestD7', $this->MODULE_ID, '\Academy\D7\Event', 'eventHandler');
	}


	function InstallDB()
	{

		\Bitrix\Main\Loader::includeModule($this->MODULE_ID);

		//Нужно использовать методы ORM  и методы битрикс для работы с БД  на низком уровне(в том числе методы DDL) для создании тыблиц
		$tableName = Base::getInstance('\Academy\D7\BookTable')->getDBTableName();

		//проверяем на существование таблицу методом isTableExists от объекта текущего соединения Application::getConnection.
		//\Academy\D7\BookTable::getConnectionName() - получить имя подключения
		if (!Application::getConnection(\Academy\D7\BookTable::getConnectionName())->isTableExists($tableName)) {
			// создание таблицы по описанию ORM, до этого нужно проверить на существование
			Base::getInstance('\Academy\D7\BookTable')->createDbTable();
		}





		// таблицы для связей 1:N
		$tableName = Base::getInstance('\Academy\D7\Author_1nTable')->getDBTableName();
		if (!Application::getConnection(\Academy\D7\Author_1nTable::getConnectionName())->isTableExists($tableName)) {
			Base::getInstance('\Academy\D7\Author_1nTable')->createDbTable();
		}

		// таблицы для связей 1:N
		$tableName = Base::getInstance('\Academy\D7\Book_1nTable')->getDBTableName();
		if (!Application::getConnection(\Academy\D7\Book_1nTable::getConnectionName())->isTableExists($tableName)) {
			Base::getInstance('\Academy\D7\Book_1nTable')->createDbTable();
		}




		// таблицы для связей M:N
		$tableName = Base::getInstance('\Academy\D7\Book_mnTable')->getDBTableName();
		if (!Application::getConnection(\Academy\D7\Book_mnTable::getConnectionName())->isTableExists($tableName)) {
			Base::getInstance('\Academy\D7\Book_mnTable')->createDbTable();
		}
		$tableName = Base::getInstance('\Academy\D7\Author_mnTable')->getDBTableName();
		if (!Application::getConnection(\Academy\D7\Author_mnTable::getConnectionName())->isTableExists($tableName)) {
			Base::getInstance('\Academy\D7\Author_mnTable')->createDbTable();
		}
		$tableName = Base::getInstance('\Academy\D7\BookAuthorsUs_mnTable')->getDBTableName();
		if (!Application::getConnection(\Academy\D7\BookAuthorsUs_mnTable::getConnectionName())->isTableExists($tableName)) {
			Base::getInstance('\Academy\D7\BookAuthorsUs_mnTable')->createDbTable();
		}

	}

	function UnInstallDB()
	{
		\Bitrix\Main\Loader::includeModule($this->MODULE_ID);

		$tableName = Base::getInstance('\Academy\D7\BookTable')->getDBTableName();
		// у текущего соединения Application::getConnection нет метода на удаление, поэтому пишем прямой запрос (используя метод queryExecute)
		Application::getConnection(\Academy\D7\BookTable::getConnectionName())->queryExecute('drop table if exists ' . $tableName);



		// таблицы для связей 1:N
		$tableName = Base::getInstance('\Academy\D7\Author_1nTable')->getDBTableName();
		Application::getConnection(\Academy\D7\Author_1nTable::getConnectionName())->queryExecute('drop table if exists ' . $tableName);
		// таблицы для связей 1:N
		$tableName = Base::getInstance('\Academy\D7\Book_1nTable')->getDBTableName();
		Application::getConnection(\Academy\D7\Book_1nTable::getConnectionName())->queryExecute('drop table if exists ' . $tableName);


		// таблицы для связей M:N
		$tableName = Base::getInstance('\Academy\D7\Book_mnTable')->getDBTableName();
		Application::getConnection(\Academy\D7\Book_mnTable::getConnectionName())->queryExecute('drop table if exists ' . $tableName);
		$tableName = Base::getInstance('\Academy\D7\Author_mnTable')->getDBTableName();
		Application::getConnection(\Academy\D7\Author_mnTable::getConnectionName())->queryExecute('drop table if exists ' . $tableName);
		$tableName = Base::getInstance('\Academy\D7\BookAuthorsUs_mnTable')->getDBTableName();
		Application::getConnection(\Academy\D7\BookAuthorsUs_mnTable::getConnectionName())->queryExecute('drop table if exists ' . $tableName);



		//Удаляем все настройки модуля
		\Bitrix\Main\Config\Option::delete($this->MODULE_ID);
	}


	// Определяем собственные права, если не описать этот метод, будут стандартные права(закрыт, просмотр, запись)
	function GetModuleRightList()
	{
		return array(
			"reference_id" => array("D", "K", "S", "W"),
			"reference"    => array(
				"[D] " . Loc::getMessage('ACADEMY_D7_DENIED'),
				"[K] " . Loc::getMessage('ACADEMY_D7_COMPONENT'),
				"[S] " . Loc::getMessage('ACADEMY_D7_SETTINGS'),
				"[W] " . Loc::getMessage('ACADEMY_D7_FULL'),
			)
		);
	}

}
