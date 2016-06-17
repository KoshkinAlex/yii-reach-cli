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
	use ConsoleCommandTraits\Help;
	use ConsoleCommandTraits\TerminalWidth;

	/** @var string Set default command action to 'actionHelp' */
	public $defaultAction = 'help';

	/**
	 * @deprecated
	 * @var bool If is terminal supports text color
	 */
	public $useColors = true;

	/** @var bool|string Convert output to this encoding  */
	public $encodeOutput = false;

	/** @var string Default label for message with success status */
	protected $labelSuccess = 'OK';

	/** @var string Default label for message with fail status */
	protected $labelFail = 'FAIL';

	/** @var string|array|integer|null Default colour for headers */
	protected $colourDefaultHeader = RCli::FONT_BLUE;

	/** @var string|array|integer|null Default colour for header lines */
	protected $colourDefaultHeaderLines = RCli::FONT_BLUE;

	/** @var bool If is true script generates output to standard output */
	private $_outputEnabled = true;

	/** @var bool If is true reach output (colours and other formatting) is enabled */
	private $_reachOutputEnabled = true;

	/** @inheritdoc */
	public function __construct($name, $runner)
	{
		parent::__construct($name, $runner);

		// By default output is enabled if we detect that script is executed by human
		$this->_outputEnabled = TerminalInfo::isExecutedByHuman();

		// Remember time of script begin execution ConsoleCommandTraits\Timer
		if (method_exists($this, 'startExecutionTime')) {
			$this->startExecutionTime();
		}

		// Windows terminal does not support colors and UTF
		if (TerminalInfo::isWindowsConsole()) {
			$this->useColors = false;
			$this->disableReachOutput();
		}

		RCli::$lineWidth = $this->getTerminalWidth();
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
	 *
	 * @return bool
	 */
	public function hasOutput()
	{
		return (bool)$this->_outputEnabled;
	}

	/**
	 * Disable reach output
	 * @return void
	 */
	public function disableReachOutput()
	{
		RCli::$useColors = $this->_reachOutputEnabled = false;
	}

	/**
	 * Enable reach output
	 * @return void
	 */
	public function enableReachOutput()
	{
		RCli::$useColors = $this->_reachOutputEnabled = true;
	}

	/**
	 * Check if reach output is enabled
	 *
	 * @return bool
	 */
	public function reachOutputEnabled()
	{
		return (bool)$this->_reachOutputEnabled;
	}

	/**
	 * Heuristic check that script is executed by human. Terminal sessions variables are serving as indicators
	 * @deprecated
	 *
	 * @see TerminalInfo::isExecutedByHuman()
	 * @return bool
	 */
	public function isExecutedByHuman()
	{
		return TerminalInfo::isExecutedByHuman();
	}

	/**
	 * Detect run on windows console
	 *
	 * @deprecated
	 * @see TerminalInfo::isExecutedByHuman()
	 * @return bool
	 */
	public function isWindowsConsole()
	{
		return TerminalInfo::isWindowsConsole();
	}

	/**
	 * Print message to user
	 *
	 * @param $message
	 * @param string|array|integer|null $color
	 */
	public function msg($message, $color = null)
	{
		$this->out(RCli::msg($message, $color));
	}

	/**
	 * Asks user to confirm by typing y or n.
	 *
	 * @param string $message
	 * @param string|array|integer|null $color Message decorate code(s)
	 * @param bool $default
	 * @return bool
	 */
	public function confirm($message, $color = null, $default = false)
	{
		$message = RCli::msg($message, $color);

		return parent::confirm($message, $default);
	}

	/**
	 * Reads user input
	 * Pay attention that \CConsoleCommand::prompt() uses readline extension if it's installed. In this case reach output is not available.
	 *
	 * @see \CConsoleCommand::prompt()
	 * @param string $message
	 * @param string|array|integer|null $color Message decorate code(s)
	 * @param null $default
	 * @return mixed
	 */
	public function prompt($message, $color = null, $default = null)
	{
		$message = extension_loaded('readline')
			? $message
			: RCli::msg($message, $color);

		return parent::prompt($message, $default);
	}

	/**
	 * Does the same as @see msg(), but adds end of line after string output
	 *
	 * @param string $message
	 * @param string|array|integer|null $color Message decorate code(s)
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

			if (method_exists($this, 'transliterate')) {
				$message = $this->transliterate($message);
			}

			echo $message;
		}
	}

	/**
	 * Output table row
	 *
	 * @see RCli::tableRow()
	 * @param $data
	 * @param int $defaultWidth
	 * @param string|array|integer|null $defaultColor
	 */
	public function table($data, $defaultWidth = 10, $defaultColor = null) {
		$this->out(RCli::tableRow($data, $defaultWidth, $defaultColor));
	}

	/**
	 * Output horizontal line with line ending
	 *
	 * @param string $char
	 * @param string|array|integer|null $color Message decorate code(s)
	 * @return void
	 */
	public function hr($char = '=', $color = null) {
		$this->out(RCli::hr($char, $color));
	}

	/**
	 * Header for some text, separated with horizontal lines
	 *
	 * @param $message
	 * @param string|array|integer|null $codes Message decorate code(s)
	 * @param string|array|integer|null $lineCodes Horizontal lines decorate code(s)
	 * @return string
	 */
	public function header($message, $codes = null, $lineCodes = null) {

		if ($codes === null) {
			$codes = $this->colourDefaultHeader;
		}

		if ($lineCodes === null) {
			$codes = $this->colourDefaultHeaderLines;
		}

		$this->out(RCli::header($message, $codes, $lineCodes));
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
		$statusLength = max(strlen($this->labelSuccess), strlen($this->labelFail)) + 1;
		$labelLength = $this->getTerminalWidth() - $statusLength - 3;

		$this->msg(sprintf(" %'.-{$labelLength}s ", $msg.' '), RCli::FONT_WHITE);
		if ($status) {
			$this->line(sprintf("%{$statusLength}s", $value !== false ? $value : $this->labelSuccess), RCli::FONT_GREEN);
		} else {
			$this->line(sprintf("%{$statusLength}s", $value !== false ? $value : $this->labelFail), RCli::FONT_RED);
		}
	}

	/**
	 * @param int $repeat Number of EOL to print
	 */
	public function eol($repeat = 1)
	{
		$this->out(str_repeat(PHP_EOL, $repeat));
	}


}