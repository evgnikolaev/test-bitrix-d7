<?

use Bitrix\Main\Localization\Loc;

//работа с settings.php
$install_count = \Bitrix\Main\Config\Configuration::getInstance()->get('academy_module_d7');

if (!check_bitrix_sessid()) {
	return;
}

if ($ex = $APPLICATION->GetException()) {

	echo CAdminMessage::ShowMessage([
		'TYPE'    => 'ERROR',
		'MESSAGE' => Loc::getMessage('MOD_UNINST_ERR'),
		"DETAILS" => $ex->GetString(),
		"HTML"    => true,
	]);
} else {
	echo CAdminMessage::ShowNote(GetMessage("MOD_INST_OK"));
}

$install_count = \Bitrix\Main\Config\Configuration::getInstance()->get('academy_module_d7');

//работа с settings.php
echo CAdminMessage::ShowMessage([
	'TYPE'    => 'OK',
	'MESSAGE' => Loc::getMessage('ACADEMY_D7_UNINSTALL_COUNT') . $install_count['uninstall'],
]);
?>


<form action="<?= $APPLICATION->GetCurPage() ?>">
	<input type="hidden" name="lang" value="<?= LANGUAGE_ID ?>">
	<input type="submit" name="" value="<?= Loc::getMessage('MOD_BACK') ?>">
</form>