<?
namespace Academy\D7;

// Класс, Собственное исключение, деление на ноль
class DivisionError extends \Bitrix\Main\SystemException
{

	protected $parameter1;
	protected $parameter2;

	public function __construct($type = 'division zero', $parameter1 = 0, $parameter2 = 0, \Exception $previous = null)
	{
		$message = 'An error has occured: ' . $type;
		$this->parameter1 = $parameter1;
		$this->parameter2 = $parameter2;
		parent::__construct($message, false, false, false, $previous);
	}

	public function getParameters1()
	{
		return $this->parameter1;
	}


	public function getParameters2()
	{
		return $this->parameter2;
	}

}