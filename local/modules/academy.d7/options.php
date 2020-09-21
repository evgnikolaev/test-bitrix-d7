<?

//имя переменной - обязательно такое, иначе права доступа не работают
$module_id = "academy.d7";

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Config\Option;

IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"] . BX_ROOT . "/modules/main/options.php");
IncludeModuleLangFile(__FILE__);

// проверяем доступ
if ($APPLICATION->GetGroupRight($module_id) < 'S') {
	$APPLICATION->AuthForm(Loc::getMessage('ACCESS_DENIED'));
}


\Bitrix\Main\Loader::includeModule($module_id);

// для получения данных, которые пользователь отправит
$request = \Bitrix\Main\HttpApplication::getInstance()->getContext()->getRequest();



//------------------------------------   ОПИСАНИЕ ОПЦИЙ  ----------------------------------------------------------------
// Каждый элемент массива - это новая вкладка
$aTabs = [
	[
		//идентификатор вкладки, для js
		"DIV"     => "edit1",
		//имя вкладки
		"TAB"     => Loc::getMessage("ACADEMY_D7_TAB_SETTINGS"),
		//всплывающая подсказка вкладки
		"TITLE" => 'всплывающая подсказка вкладки',
		//массив опция на данной вкладки, определенной структуры
		"OPTIONS" => [
			"Служебные настройки",
				// ключ опции(имя формы)  ,  имя поля  ,  значение по умолчанию(пустое, если в БД не задано)   , массив поля формы ([тип, параметры])
			['field_text', Loc::getMessage('ACADEMY_D7_FIELD_TEXT_TITLE'), '', ['textarea', 10, 50]],
			['field_line', Loc::getMessage('ACADEMY_D7_FIELD_LINE_TITLE'), '', ['text', 10]],
			['field_list', Loc::getMessage('ACADEMY_D7_FIELD_LIST_TITLE'), '', ['multiselectbox', ['var1' => 'var1', 'var2' => 'var2', 'var3' => 'var3', 'var4' => 'var4',]]],
		]
	],
	[
		"DIV"   => "edit2",
		"TAB"   => Loc::getMessage("MAIN_TAB_RIGHTS"),
		"TITLE" => GetMessage("MAIN_TAB_TITLE_RIGHTS")
	],
];


//------------------------------------   СОХРАНЕНИЕ  ---------------------------------------------------------------
if ($request->isPost() && $request['Update'] && check_bitrix_sessid()) {
	foreach ($aTabs as $aTab) {

		//или можо использовать  __AdmSettingsSaveList($MODULE_ID, $arOption);
		foreach ($aTab['OPTIONS'] as $arOption) {

			//Строка с подсветкой. Используется для разделения настроек во вкладке
			if (!is_array($arOption)) {
				continue;
			}

			//Уведомление с подсветкой
			if ($arOption['note']) {
				continue;
			}

			//или __AdmSettingsSaveList($MODULE_ID, $arOption);
			$optionName = $arOption[0];
			$optionValue = $request->getPost($optionName);
			Option::set($module_id, $optionName, is_array($optionValue) ? implode(",", $optionValue) : $optionValue);

		}
	}
}



//------------------------------------   ВИЗУАЛЬНЫЙ ВЫВОД  ---------------------------------------------------------------
$tabControl = new CAdminTabControl('tabControl', $aTabs);//указываем id 'tabControl'
$tabControl->Begin();
	//mid  - id модуля
	?>
	<form method="post"
		  action="<?= $APPLICATION->GetCurPage() ?>?mid=<?= htmlspecialcharsbx($request['mid']) ?>&amp;lang=<?= $request['lang'] ?>"
		  name="academy_d7_settings">

		<? foreach ($aTabs as $aTab) {
			if ($aTab['OPTIONS']) {
				//открываем вкладку и рисуем
				$tabControl->BeginNextTab();
				__AdmSettingsDrawList($module_id, $aTab['OPTIONS']);
			}
		}

		// последняя вкладка с правами доступа рисуется отдельно
		$tabControl->BeginNextTab();
		//добавляется возможность управления правами доступа
		require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/admin/group_rights.php");

		// Отправляем кнопки далее
		$tabControl->Buttons();
		?>

		<input type="submit" name="Update" value="<?= GetMessage('MAIN_SAVE') ?>">
		<input type="submit" name="reset" value="<?= GetMessage('MAIN_RESET') ?>">
		<?= bitrix_sessid_post(); ?>


	</form>

<?
$tabControl->End();
?>