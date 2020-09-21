<?

namespace Academy\D7;

use Bitrix\Main\Entity;
use Bitrix\Main\Entity\Event;
use Bitrix\Main\Type;

class Book_1nTable extends Entity\DataManager
{

	public static function getTableName()
	{
		return 'book_d7_2';
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

			//ID Связанного автора
			new Entity\IntegerField('AUTHOR_ID'),

			//Описываем эту связь
			//Это виртуальное поле, не имеющая отражение в БД
			new Entity\ReferenceField(
					// к этому полю мы будем обращаться, когда нам нужна будет связь
				'AUTHOR',
					// класс сущности с которым мы строим связь
				'\Academy\D7\Author_1nTable',
					// описываем по каким полям связываем сущность. Описываем в формате, похожим на фильтр в gtlist.
					// Ключами и значениями являются имена полей с префиксами
					// this - поле текущей сущности, ref - поле сущности партнера
				['=this.AUTHOR_ID' => 'ref.ID']
					// можно указать 4 параметр - тип подключения
					// LEFT - по умолчанию, RIGHT или INNER
			)
		];
	}

}