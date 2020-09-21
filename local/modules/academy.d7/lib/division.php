<?


namespace Academy\D7;

// просто ф-ия деления, в которой выбрасываем исключение
class Division
{
	public static function divided($parameter1 = 0, $parameter2 = 0)
	{
		if ($parameter2 === 0) {
			throw new \Academy\D7\DivisionError('Деление на ноль', $parameter1, $parameter2);
		}

		$result = $parameter1 / $parameter2;
		return $result;
	}
}
