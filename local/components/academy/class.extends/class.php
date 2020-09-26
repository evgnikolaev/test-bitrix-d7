<?php


if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;

//от какого компонента наследуемся, после этого удет доступен класс компонента, от которого наследуемся
CBitrixComponent::includeComponentClass('academy:class');

//Имя класса произвольное, но должно наследоваться от  класса компонента
class classComponentsExtends extends classComponents
{
	function var2()
	{
		$arResult['var2'] = 'Отработал метод var22 extends';
		return $arResult;
	}

	//выполнение компонента, переопределяем, чтобы был доступен var2
	// если код нужно вставить в произвольное место, то executeComponent нужно переопределить полностью
	function executeComponent()
	{
		$this->arResult = array_merge($this->arResult,$this->var2());
		parent::executeComponent();
	}

}