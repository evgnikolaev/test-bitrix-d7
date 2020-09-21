<?

namespace Academy\D7;


use Bitrix\Main\Entity;
use Bitrix\Main\Type;

class Author_mnTable extends Entity\DataManager
{

	public static function getTableName()
	{
		return 'author_d7_mn';
	}

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