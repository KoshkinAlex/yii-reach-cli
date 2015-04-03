<?php
/** @author: Koshkin Alexey <koshkin.alexey@gmail.com> */

/**
 * Class ConsoleCommand
 * Base class for console commands (Yii framework 1.*)
 */
class ConsoleCommand extends CConsoleCommand
{
	const CHANGES_DO_NOT = 0;
	const CHANGES_DO_AUTOMATIC = 1;
	const CHANGES_DO_CONFIRM = 2;

	public $description = null;
	public $defaultAction = 'help';

	/**
	 * Список описаний действий комманды
	 * @var array
	 */
	public $actionDescription = [
		'help' => 'Справка по доступным действиям комманды',
	];

	/** @var bool If is terminal supports text color */
	public $useColors = true;

	/** @var bool If is terminal needs to replace some chharacters */
	public $useTransliteration = false;

	/** @var array Transliteration table used if $this->useTransliteration is true */
	public $transliterationTable = [ 'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd', 'е' => 'e', 'ё' => 'yo', 'ж' => 'zh', 'з' => 'z', 'и' => 'i', 'й' => 'yi', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n', 'о' => 'o', 'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't', 'у' => 'u', 'ф' => 'f', 'х' => 'x', 'ц' => 'c', 'ч' => 'ch', 'ш' => 'sh', 'щ' => 'sh', 'ъ' => '', 'ы' => 'y', 'ь' => '', 'э' => 'e', 'ю' => 'yu', 'я' => 'ya', 'А' => 'a', 'Б' => 'b', 'В' => 'v', 'Г' => 'g', 'Д' => 'd', 'Е' => 'e', 'Ё' => 'yo', 'Ж' => 'zh', 'З' => 'z', 'И' => 'i', 'Й' => 'yi', 'К' => 'k', 'Л' => 'l', 'М' => 'm', 'Н' => 'n', 'О' => 'o', 'П' => 'p', 'Р' => 'r', 'С' => 's', 'Т' => 't', 'У' => 'u', 'Ф' => 'f', 'Х' => 'x', 'Ц' => 'c', 'Ч' => 'ch', 'Ш' => 'sh', 'Щ' => 'sh', 'Ъ' => '', 'Ы' => 'y', 'Ь' => '', 'Э' => 'e', 'Ю' => 'yu', 'Я' => 'ya',];

	/** @var bool|string Convert output to this encoding  */
	public $encodeOutput = false;

	/** @var bool If is true script generates output to standart output */
	private $_outputEnabled = true;

	/** @var int Script begin execution timestamp. Used for statistic. */
	private $_timeBegin = 0;

	/** @var array Mapping of counter keys to human-readable indicator labels */
	private $_counterLabel = [];

	/** @var array Values of counter keys */
	private $_counter = [];

	/** @inheritdoc */
	public function __construct($name, $runner)
	{
		parent::__construct($name, $runner);

		// By default output is enabled if we detect that script is executed by human
		$this->_outputEnabled = $this->isExecutedByHuman();

		// We need it to measure script execution time
		$this->_timeBegin = microtime(true);

		// Windows terminal does not support colors and UTF
		if ($this->isWindowsConsole()) {
			$this->useColors = false;
			$this->useTransliteration = true;
			RCli::$eol = "\n";
		}
	}

	/**
	 * Определение массива с названиями типов изменений
	 * @param null $id
	 * @return array|string
	 */
	public static function getDoСhangesType($id = null) {

		$list = [
			self::CHANGES_DO_NOT => 'Не вносить правки в БД',
			self::CHANGES_DO_AUTOMATIC => 'Вносить правки в БД для всех случаев',
			self::CHANGES_DO_CONFIRM => 'Выдавать запрос на внесение правок в БД',
		];

		if ($id !== null && !isset($list[$id])) {
			return '';
		}
		return $id !== null ? (is_array($id) ? array_intersect($list, $id) : $list[$id]) : $list;
	}

	/**
	 * Получение описания действия комманды для формирования описания
	 * @param null $actionID
	 * @return string
	 */
	public function getActionDescription($actionID = null) {
		return $actionID !== null && isset($this->actionDescription[$actionID]) ? $this->actionDescription[$actionID] : '';
	}

	/**
	 * Отображение всех возможных действий с описаниями
	 * @return string
	 */
	public function getHelp()
	{
		$class=new ReflectionClass(get_class($this));
		$help = RCli::hr('=', RCli::CODE_BRIGHT_LESS).' '.RCli::writeString($class->getName(), [RCli::CODE_FONT_RED, RCli::CODE_UNDERLINE]);
		$description = $this->description;
		if (!$description) {
			$description = $this->getDocText($class->getDocComment());
		}
		if ($description) {
			$help .= RCli::$eol.'	'.RCli::writeString($description, RCli::CODE_FONT_YELLOW).RCli::$eol;
		}
		$help .= RCli::$eol." ".RCli::writeString("Доступные действия", RCli::CODE_UNDERLINE).":".RCli::$eol.RCli::$eol;

		foreach($class->getMethods(ReflectionMethod::IS_PUBLIC) as $method)
		{
			$name=$method->getName();
			if(!strncasecmp($name,'action',6) && strlen($name)>6)
			{
				$actionName=substr($name,6);
				$actionName[0]=strtolower($actionName[0]);
				$help.= "	".Yii::app()->rcli->writeString($actionName, [RCli::CODE_FONT_RED, RCli::CODE_BRIGHT_MORE]);
				if ($this->defaultAction == $actionName) {
					$help .= ' ' . RCli::writeString('[default]', RCli::CODE_FONT_GREEN) . ' ';
				}
				$description = $this->getActionDescription($actionName);
				$docComment = $method->getDocComment();
				if (!$description) {
					$description = $this->getDocText($docComment);
				}
				if ($description) $help.= " - ".RCli::writeString($description, RCli::CODE_FONT_YELLOW);
				$help.= RCli::$eol;

				foreach ($method->getParameters() as $param) {

					$defaultValue = $param->isDefaultValueAvailable() ? print_r($param->getDefaultValue(),1) : null;
					$name = $param->getName();

					$description = '';

					if (preg_match("/{$name}\s*([^\@\*\n]+)[\@\*\n]/is", $docComment, $m)) {
						$description = ' ' . RCli::writeString($m[1], RCli::CODE_FONT_WHITE);
					}

					$help .= RCli::writeString(
							"		" .
							($param->isOptional()
								? "[--$name=$defaultValue]"
								: "--$name=value"
							),
							RCli::CODE_FONT_BLUE
						) . $description . RCli::$eol;

				}
				$help .= RCli::$eol;
			}
		}
		return $help.RCli::$eol;
	}

	/**
	 * Действие по умолчанию - отображение справки
	 */
	public function actionHelp(){

		print $this->getHelp();
	}


	/**
	 * Выдача запроса на тип изменений (автоматический, без изменений, по запросу)
	 * @return int
	 */
	public function promtDoChanges(){
		$text = '';
		$c = [];
		$i = 0;
		foreach (self::getDoСhangesType() as $k=>$v) {
			$i++;
			$text .= "   ".Yii::app()->rcli->writeString("{$i})", RCli::CODE_FONT_RED). " ".$v."\n";
			$c[$i] = $k;
		}

		echo Yii::app()->rcli->writeString("\nВыберите тип внесения правок в БД:\n", [RCli::CODE_FONT_RED, RCli::CODE_BRIGHT_MORE]);
		echo $text;

		$type = $this->prompt("\nВведите число");

		if (isset($c[$type])) return $c[$type];
		else return self::CHANGES_DO_NOT;
	}

	/**
	 * Heuristic check that script is executed by human. Terminal sessions variables are serving as indicators.
	 *
	 * @return bool
	 */
	public function isExecutedByHuman()
	{
		return

			//Linux
			isset($_SERVER['SSH_CLIENT'])
			|| isset($_SERVER['TERM'])
			|| isset($_SERVER['SSH_TTY'])
			|| isset($_SERVER['SSH_CONNECTION'])

			// Windows
			|| isset($_SERVER['HOMEPATH'])
			||  isset($_SERVER['USERNAME']);
	}

	/**
	 * Detect run on windows console
	 * @return bool
	 */
	public function isWindowsConsole()
	{
		return
			isset($_SERVER['PATHEXT'])
			|| isset($_SERVER['windir']);
	}

	/**
	 * Print message to user
	 *
	 * @param $message
	 * @param null $color
	 */
	public function msg($message, $color = null)
	{
		// Maybe this is unnecessary
		$colorAlias = [
			'red' => RCli::CODE_FONT_RED,
			'blue' => RCli::CODE_FONT_BLUE,
			'green' => RCli::CODE_FONT_GREEN,
		];

		if ($color && !empty($colorAlias[$color])) {
			$color = $colorAlias[$color];
		}

		if (!$color) {
			$color = RCli::CODE_DEFAULT;
		}

		if ($this->useColors) {
			$this->out(RCli::writeString($message, $color));
		} else {
			$this->out($message);
		}
	}

	/**
	 * Asks user to confirm by typing y or n.
	 *
	 * @param string $message
	 * @param null $color
	 * @param bool $default
	 * @return bool
	 */
	public function confirm($message, $color = null, $default = false)
	{
		$message = RCli::writeString($message, $color);
		return parent::confirm($message, $default);
	}


	/**
	 * Does the same as @see msg(), but adds end of line after string output
	 *
	 * @param string $message
	 * @param null $color
	 */
	public function line($message, $color = null)
	{
		$this->msg($message, $color);
		$this->out(RCli::$eol);
	}

	/**
	 * Wrapper for all output generated by this class.
	 * Output can be catched and reformatted it it's necessary
	 *
	 * @param string $message
	 */
	protected function out($message)	{
		if ($this->_outputEnabled) {
			if ($this->useTransliteration) echo strtr($message, $this->transliterationTable);
			else echo $message;
		}
	}

	protected function hr($char = '=', $color = null) {
		$this->line(str_repeat($char, (int)RCli::$lineWidth), $color);
	}

	/**
	 * Increment value of parameter that is interesting for us in final statistic
	 *
	 * @param string|bool $key Human friendly label of parameter (must be exactly the same in all calls)
	 * @param integer $count Increment step
	 * @return integer After increment parameter value
	 */
	public function inc($key = false, $count = 1)
	{
		if (!empty($key) && is_string($key)) {
			$realKey = md5($key);
			$this->_counterLabel[$realKey] = $key;
			$key = $realKey;
		}

		if (empty($this->_counter[$key])) {
			$this->_counter[$key] = 0;
		}
		$this->_counter[$key] += $count;

		return $this->_counter[$key];
	}

	/**
	 * Print command statistic
	 *  - All parameters that we collect in @see inc()
	 *  - Script execution time
	 */
	public function printStat()
	{
		if (count($this->_counter) == 0) {
			return;
		}

		$this->eol();
		$this->out(RCli::hr('=', RCli::CODE_FONT_YELLOW));

		if ($this->_timeBegin > 0) {
			$this->out(
				RCli::writeString(
					sprintf("Script execution time: %.1f sec", microtime(true) - $this->_timeBegin),
					RCli::CODE_FONT_YELLOW
				)
			);
			$this->eol();
		}

		if ($this->_outputEnabled) {
			foreach ($this->_counter as $key => $value) {
				$label = empty($this->_counterLabel[$key]) ? "Number of records" : $this->_counterLabel[$key];
				echo RCli::writeString("   " . $label . ": ", RCli::CODE_FONT_WHITE);
				echo RCli::writeString($value, RCli::CODE_FONT_GREEN);
				$this->eol();
			}
		}

		$this->eol(2);
	}

	/**
	 * Output string and boolean result of some action
	 *
	 * @param string $msg Custom message (action name)
	 * @param boolean $status Action status
	 * @param false|mixed $value Indicator value. If false OK|FAIL strings are used, according to $status value
	 */
	public function status($msg, $status, $value = false)
	{
		$this->out(RCli::writeString(sprintf("%'.-70s ", $msg), RCli::CODE_FONT_WHITE));
		if ($status) {
			$this->out(RCli::writeString(sprintf("%8s", $value !== false ? $value : "OK"), RCli::CODE_FONT_GREEN));
		} else {
			$this->out(RCli::writeString(sprintf("%8s", $value !== false ? $value : "FAIL"), RCli::CODE_FONT_RED));
		}

		$this->eol();
	}

	/**
	 * @param int $repeat Number of EOL to print
	 */
	public function eol($repeat = 1)
	{
		$this->out(str_repeat(RCli::$eol, $repeat));
	}

	/**
	 * Вывод сообщения об ошибке
	 * @param string $msg
	 */
	public function error($msg) {
		$this->msg("ERROR: ", RCli::CODE_FONT_RED);
		$this->msg($msg, RCli::CODE_FONT_YELLOW);
		$this->out(RCli::$eol);
		Yii::app()->end();
	}

	/**
	 * Получение описания из PHPDoc комментария
	 * @param $doc
	 * @return string
	 */
	protected function getDocText($doc) {
		$description = '';
		foreach (explode("\n", explode('@', $doc, 2)[0]) as $line) {
			$line = trim($line, " \t\n\r\0\x0B/*.");
			if ($line) $description .= $line.". ";
		}

		return $description;
	}
}