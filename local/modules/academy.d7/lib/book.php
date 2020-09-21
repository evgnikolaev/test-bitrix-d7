<?

namespace Academy\D7;

use Bitrix\Main\Entity;
use Bitrix\Main\Entity\Event;
use Bitrix\Main\Type;


//  должен быть унаследован от Entity\Datamanager
//  имя класса - имя сущности + Table
//  При этом автозагрузка файлов все равно работает
class BookTable extends Entity\DataManager
{

	// возвращается имя таблицы
	// не обязательно, тогда имя будет 'b_academy_d7_book' (namespase + class)
	public static function getTableName()
	{
		return 'book_d7';
	}


	//  возвращает имя подключения к БД, если мы определили несколько подлючений в файле .settings.php
	//  (означает, что данные ORM-сущности будут храниться в другой БД)
	//  Если не указать, будет по умолчанию
	/*public static function getConnectionName()
	{
		return 'default';
	}*/


	public static function getUfId()
	{
		return 'BOOK_D7';
	}


	//Описание самой ORM-сущности
	public static function getMap()
	{

		/*
		 Типы полей (Наследники от ScalarField)

									             Field
									               |
											   ScalarField
												   |
		----------------------------------------------------------------------------------------------
		|                     |                 |                   |               |               |
	DataField           BooleanField        IntegerField        FloatField      EnumField       StringField
	DateTimeField                                                                               TextField


		IntegerField - целое число
		FloatField   - число
		StringField - строка
		TextField   - текст
		DataField   - дата
		DateTimeField - дата, время
		BooleanField - да, нет
		EnumField   - значение из списка



		new Entity\IntegerField(
			'ID',  - имя, должно быть уникальнымд для одной сущности, в верхнем регистре
			 [	'primary'      => true,  - первичниый ключ
				'autocomplete' => true  - автоинкремент
				'reqired' => true,      - обязательность
				'column_name' => 'ISBN_CODE', - в таблице будет колонка ISBN_CODE, мы же будем обращаться по ID (или ISBN, пример с ISBN)
												Можно создать несколько ORM-полей с одним column_name (то есть будет несколько ORM-полей, а в таблице будет одно поле)
				'default_value'         - значение по умолчанию, может быть любой callable (имя ф-ии, массив из класса объекта)
				'validation'            - валидаторы
		]),

		Предусмотрено не только хранение данных как есть, но их преобразование при выборке.
		Например, помимо года издания, хотим получать возраст книги в годах.
		Хранить число в БД накладно, придется каждый раз пересчитывать данные и обновлять в БД.
		Можно считать возраст на стороне БД, для этого используем ExpressionField.
		!!! ExpressionField - можно использовать только в выборке данных, так как физически таких полей нет !!!

		new Entity\ExpressionField(
			'AGE_YEAR',                 - имя
			'YEAR(CURDATE())-%s',       - sql выражение, при этом другие поля сущности нужно заменить на поля сущности согласно формату sprintf  ( %s )
											писать можно любые sql-выражения, допустимые в select expression.
			[ 'RELEASED' ]              - поля сущности по порядку
		),

		*/

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
			//Год выхода
			new Entity\IntegerField('RELEASED', [
				'required' => true
			]),
			//ISBN
			new Entity\StringField('ISBN', [
				'required'    => true,
				'column_name' => 'ISBN_CODE',

				//Валидаторы - callback, который возвращает массив валидаторов.
				'validation'  => function () {
					return [
						new Entity\Validator\Unique(),

						//собственный валидатор (цифры, разделенные пробелами и дефисами, цифр должно быть не более 13)
						function ($value, $primary, $row, $field) {
							//$value - значение поля
							//$primary - массив  с первичным ключом, в данном случае [ID=>1]
							//$row -  весь массив данных, переданный в add:: или update::
							//$field - обект валидируемого поля Entity\StringField('ISBN', ...)

							$clean = str_replace(['-', ' '], '', $value);

							if (preg_match('/^\d{1,13}$/', $clean)) {
								return true;
							} else {
								return 'Код ISBN должен содержать не болеее 13 цифр';
							}

						}

					];
				}
			]),
			//ФИО автора
			new Entity\StringField('AUTHOR'),
			//Дата и время поступления книги в магазин
			new Entity\DatetimeField('TIME_INTERVAL', [
				'required'      => true,
				// вернется текущее дата время
				'default_value' => new \Bitrix\Main\Type\DateTime,
			]),
			//Описание книги
			new Entity\TextField('DESCRIPTION'),

			new Entity\ExpressionField('AGE_YEAR', 'YEAR(CURDATE())-%s', [
				'RELEASED'
			]),

			//количество редактирование элемента (для вычисляемых значений - 18 урок)
			new Entity\IntegerField('WRITE_COUNT'),
		];
	}


	//Событие сразу при описании ORM-сущности
	public static function onBeforeUpdate( Entity\Event $event)
	{
		// получаем объект события результата
		$result = new Entity\EventResult();
		//получаем поля, изменяемые при обновлении
		$data = $event->getParameter('fields');

		if (isset($data['ISBN'])) {
			echo "Обработчик файла bookOrm.php";
			$result->addError(new Entity\FieldError(
				$event->getEntity()->getField('ISBN'),
				'Запрещено меять ISBN у существующих книг'
			));
		}
		return $result;
	}
}