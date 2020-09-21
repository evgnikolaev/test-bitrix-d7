<?

namespace Academy\D7;

use Bitrix\Main\Entity;
use Bitrix\Main\Entity\Event;
use Bitrix\Main\Type;

// промежуточная таблица M:N
class BookAuthorsUs_mnTable extends Entity\DataManager
{

	public static function getTableName()
	{
		return 'bookauthorsus_d7';
	}

	public static function getMap()
	{
		return [

			//ID
			new Entity\IntegerField('ID', [
				'primary'      => true,
				'autocomplete' => true
			]),

			// id связанных книг
			new Entity\IntegerField('BOOK_ID'),

			new Entity\ReferenceField(
				'BOOK',
				'\Academy\D7\Book_mnTable',
				['=this.BOOK_ID'=>'ref.ID']
			),

			// id связанных авторов
			new Entity\IntegerField('AUTHOR_ID'),

			new Entity\ReferenceField(
				'AUTHOR',
				'\Academy\D7\Author_mnTable',
				['=this.AUTHOR_ID'=>'ref.ID']
			),
		];
	}
}