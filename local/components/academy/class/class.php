<?php


if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;

//Имя класса произвольное, но должно наследоваться от  CBitrixComponent
class classComponents extends CBitrixComponent
{
	function var1()
	{
		$arResult['var1'] = 'Отработал метод var1';
		return $arResult;
	}

	//выполнение компонента
	function executeComponent()
	{
		$this->arResult = array_merge($this->arResult,$this->var1());
		$this->includeComponentTemplate();
	}

}