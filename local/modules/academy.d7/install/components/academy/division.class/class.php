<?

use Bitrix\Main;
use Bitrix\Main\Localization\Loc;


class D7Class extends CBitrixComponent
{
	var $test;


	/** Проверяет подключение необходимых модулей
	 * @return bool
	 * @throws \Bitrix\Main\LoaderException
	 */
	protected function checkModules()
	{
		if (!Main\Loader::includeModule('academy.d7')) {
			throw new Main\LoaderException(Loc::getMessage('ACADEMY_D7_MODULE_NOT_INSTALLED'));
		}
	}


	function var1()
	{

		$arResult = \Academy\D7\Division::divided(4, 2);
//		$arResult = \Academy\D7\Division::divided(4, 0);
//		$arResult = 'Доступ к компонент есть';
		return $arResult;
	}


	public function executeComponent()
	{
		global $APPLICATION;

		try {
			$this->includeComponentLang('class.php');
			$this->checkModules();


			// проверяем доступ в компоненте
			if ($APPLICATION->GetGroupRight('academy.d7') < 'K') {
				ShowError(Loc::getMessage('ACCESS_DENIED'));
			} else {
				$this->arResult[] = $this->var1();
				$this->includeComponentTemplate();
			}

			// перехватываем собственное исключение
		} catch (\Academy\D7\DivisionError $e) {
			ShowError($e->getMessage());
			var_dump('параметр1 - ' . $e->getParameters1());
			var_dump('параметр2 - ' . $e->getParameters2());
		}
	}
}