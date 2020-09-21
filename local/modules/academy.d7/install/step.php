<?

use Bitrix\Main\Localization\Loc;

//работа с settings.php
$install_count = \Bitrix\Main\Config\Configuration::getInstance()->get('academy_module_d7');

//работа с settings.php
$cache_type = \Bitrix\Main\Config\Configuration::getInstance()->get('cache');

if (!check_bitrix_sessid()) {
	return;
}


//если есть ошибка, покажем, иначе успешное сообщение
if ($ex = $APPLICATION->GetException()) {
	echo CAdminMessage::ShowMessage([
		'TYPE'    => 'ERROR',
		'MESSAGE' => Loc::getMessage('MOD_INST_ERR'),
		"DETAILS" => $ex->GetString(),
		"HTML"    => true,
	]);
} else {
	echo CAdminMessage::ShowNote(Loc::getMessage('MOD_INST_OK'));
}


//работа с settings.php
echo CAdminMessage::ShowMessage([
	'TYPE'    => 'OK',
	'MESSAGE' => Loc::getMessage('ACADEMY_D7_INSTALL_COUNT') . $install_count['install'],
]);
//работа с settings.php
if (!$cache_type['type'] || $cache_type['type'] == 'none') {
	echo CAdminMessage::ShowMessage([
		'TYPE'    => 'ERROR',
		'MESSAGE' => Loc::getMessage('ACADEMY_D7_NO_CACHE'),
	]);
}
?>
<form action="<?= $APPLICATION->GetCurPage() ?>">
	<input type="hidden" name="lang" value="<?= LANGUAGE_ID ?>">
	<input type="submit" name="" value="<?= Loc::getMessage('MOD_BACK') ?>">
</form>