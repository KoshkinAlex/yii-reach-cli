<?php
/** @author: Koshkin Alexey <koshkin.alexey@gmail.com> */

namespace ReachCli;

/**
 * Class ConsoleCommand
 * Base class for console commands (Yii framework 1.*)
 *
 * @package ReachCli
 */
abstract class ConsoleCommand extends \CConsoleCommand
{
	use ConsoleCommandTraits\Timer;
	use ConsoleCommandTraits\ErrorWarning;
	use ConsoleCommandTraits\Statistic;
	use ConsoleCommandTraits\ListSelect;

	public $description = null;
	public $defaultAction = 'help';

	/** @var bool If is terminal supports text color */
	public $useColors = true;

	/** @var bool If is terminal needs to replace some characters */
	public $useTransliteration = false;

	/** @var array Transliteration table used if $this->useTransliteration is true */
	public $transliterationTable = [ 'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd', 'е' => 'e', 'ё' => 'yo', 'ж' => 'zh', 'з' => 'z', 'и' => 'i', 'й' => 'yi', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n', 'о' => 'o', 'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't', 'у' => 'u', 'ф' => 'f', 'х' => 'x', 'ц' => 'c', 'ч' => 'ch', 'ш' => 'sh', 'щ' => 'sh', 'ъ' => '', 'ы' => 'y', 'ь' => '', 'э' => 'e', 'ю' => 'yu', 'я' => 'ya', 'А' => 'a', 'Б' => 'b', 'В' => 'v', 'Г' => 'g', 'Д' => 'd', 'Е' => 'e', 'Ё' => 'yo', 'Ж' => 'zh', 'З' => 'z', 'И' => 'i', 'Й' => 'yi', 'К' => 'k', 'Л' => 'l', 'М' => 'm', 'Н' => 'n', 'О' => 'o', 'П' => 'p', 'Р' => 'r', 'С' => 's', 'Т' => 't', 'У' => 'u', 'Ф' => 'f', 'Х' => 'x', 'Ц' => 'c', 'Ч' => 'ch', 'Ш' => 'sh', 'Щ' => 'sh', 'Ъ' => '', 'Ы' => 'y', 'Ь' => '', 'Э' => 'e', 'Ю' => 'yu', 'Я' => 'ya',];

	/** @var bool|string Convert output to this encoding  */
	public $encodeOutput = false;

	/** @var string Label that marks default action in list of available command actions */
	protected $labelDefaultAction = '[default]';

	/** @var string Default label for message with success status */
	protected $labelSuccess = 'OK';

	/** @var string Default label for message with fail status */
	protected $labelFail = 'FAIL';

	/** @var bool If is true script generates output to standard output */
	private $_outputEnabled = true;

	/** @inheritdoc */
	public function __construct($name, $runner)
	{
		parent::__construct($name, $runner);

		// By default output is enabled if we detect that script is executed by human
		$this->_outputEnabled = $this->isExecutedByHuman();

		// Remember time of script begin execution ConsoleCommandTraits\Timer
		if (method_exists($this, 'startExecutionTime')) {
			$this->startExecutionTime();
		}

		// Windows terminal does not support colors and UTF
		if ($this->isWindowsConsole()) {
			$this->useColors = false;
			$this->useTransliteration = true;
		}
	}

	/**
	 * Disable output
	 * @return void
	 */
	public function disableOutput()
	{
		$this->_outputEnabled = false;
	}

	/**
	 * Enable output
	 * @return void
	 */
	public function enableOutput()
	{
		$this->_outputEnabled = true;
	}

	/**
	 * Check if output is enabled
	 * @return bool
	 */
	public function hasOutput()
	{
		return (bool)$this->_outputEnabled;
	}

	/**
	 * Отображение всех возможных действий с описаниями
	 * @return string
	 */
	public function getHelp()
	{
		$class=new \ReflectionClass(get_class($this));
		$help = RCli::hr('=', RCli::BRIGHT_LESS).' '.RCli::msg($class->getName(), [RCli::FONT_RED, RCli::UNDERLINE]);

		$description = $this->description;
		if (!$description) {
			$description = $this->getDocText($class->getDocComment());
		}
		if ($description) {
			$help .= PHP_EOL.'	'.RCli::msg($description, RCli::FONT_YELLOW).PHP_EOL;
		}
		$help .= PHP_EOL." ".RCli::msg("Доступные действия", RCli::UNDERLINE).":".PHP_EOL.PHP_EOL;

		foreach($class->getMethods(\ReflectionMethod::IS_PUBLIC) as $method)
		{
			$name=$method->getName();
			if(!strncasecmp($name,'action',6) && strlen($name)>6)
			{
				$actionName=substr($name,6);
				$actionName[0]=strtolower($actionName[0]);
				$help.= "	".RCli::msg($actionName, [RCli::FONT_RED, RCli::BRIGHT_MORE]);
				if ($this->defaultAction == $actionName) {
					$help .= ' ' . RCli::msg($this->labelDefaultAction, RCli::FONT_GREEN) . ' ';
				}
				$docComment = $method->getDocComment();
				$description = $this->getDocText($docComment);

				if ($description) $help.= " - ".RCli::msg($description, RCli::FONT_YELLOW);
				$help.= PHP_EOL;

				foreach ($method->getParameters() as $param) {

					$defaultValue = $param->isDefaultValueAvailable() ? print_r($param->getDefaultValue(),1) : null;
					$name = $param->getName();

					$description = '';

					if (preg_match("/{$name}\s*([^\@\*\n]+)[\@\*\n]/is", $docComment, $m)) {
						$description = ' ' . RCli::msg($m[1], RCli::FONT_WHITE);
					}

					$help .= RCli::msg(
							"		" .
							($param->isOptional()
								? "[--$name=$defaultValue]"
								: "--$name=value"
							),
							RCli::FONT_BLUE
						) . $description . PHP_EOL;

				}
				$help .= PHP_EOL;
			}
		}
		return $help . PHP_EOL;
	}

	/**
	 * Действие по умолчанию - отображение справки
	 */
	public function actionHelp(){

		print $this->getHelp();
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
		if ($color && !empty($colorAlias[$color])) {
			$color = $colorAlias[$color];
		}

		if (!$color) {
			$color = RCli::CLEAR;
		}

		if ($this->useColors) {
			$this->out(RCli::msg($message, $color));
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
		$message = RCli::msg($message, $color);
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
		$this->out(PHP_EOL);
	}

	/**
	 * Console output for not only scalar variables
	 *
	 * @see RCli::outVar()
	 * @param mixed $var
	 */
	public function outVar($var)
	{
		$this->out(RCli::outVar($var));
	}

	/**
	 * Wrapper for all output generated by this class.
	 * Output can be cached and reformatted it it's necessary
	 *
	 * @param string $message
	 * @return void
	 */
	public function out($message)	{
		if ($this->_outputEnabled) {
			if ($this->useTransliteration) echo strtr($message, $this->transliterationTable);
			else echo $message;
		}
	}

	/**
	 * Output table row
	 *
	 * @see RCli::tableRow()
	 * @param $data
	 * @param int $defaultWidth
	 * @param null $defaultColor
	 */
	public function table($data, $defaultWidth = 10, $defaultColor = null) {
		$this->out(RCli::tableRow($data, $defaultWidth, $defaultColor));
	}

	/**
	 * Output horizontal line with line ending
	 *
	 * @param string $char
	 * @param null $color
	 * @return void
	 */
	public function hr($char = '=', $color = null) {
		$this->out(RCli::hr($char, $color));
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
		$labelLength = max(strlen($this->labelSuccess), strlen($this->labelFail)) + 1;
		$this->msg(sprintf("%'.-70s ", $msg), RCli::FONT_WHITE);
		if ($status) {
			$this->line(sprintf("%{$labelLength}s", $value !== false ? $value : $this->labelSuccess), RCli::FONT_GREEN);
		} else {
			$this->line(sprintf("%{$labelLength}s", $value !== false ? $value : $this->labelFail), RCli::FONT_RED);
		}
	}

	/**
	 * @param int $repeat Number of EOL to print
	 */
	public function eol($repeat = 1)
	{
		$this->out(str_repeat(PHP_EOL, $repeat));
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