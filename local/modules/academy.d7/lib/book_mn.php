<?

namespace Academy\D7;

use Bitrix\Main\Entity;
use Bitrix\Main\Entity\Event;
use Bitrix\Main\Type;

class Book_mnTable extends Entity\DataManager
{

	public static function getTableName()
	{
		return 'book_d7_mn';
	}

	//Описание самой ORM-сущности
	public static function getMap()
	{
		return [

			//ID
			new Entity\IntegerField('ID', [
				'primary'      => true,
				'autocomplete' => true
			]),

			//Название
			new Entity\StringField('NAME', [
				'reqired' => true,
			]),
		];
	}

}