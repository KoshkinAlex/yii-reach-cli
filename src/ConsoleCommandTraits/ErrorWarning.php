<?php
/**
 * @author: Koshkin Alexey <koshkin.alexey@gmail.com>
 */

namespace ReachCli\ConsoleCommandTraits;

use ReachCli\RCli;

/**
 * Class ErrorWarning
 * Errors and warnings display and log
 *
 * @package ReachCli\ConsoleCommandTraits
 */
trait ErrorWarning
{
	/** @var string Font color for warning messages text */
	protected $fontColorWarning = RCli::FONT_CYAN;

	/** @var string Font color for warning titles */
	protected $fontTitleWarning = RCli::FONT_YELLOW;

	/** @var string Font color for error messages text */
	protected $fontColorError = RCli::FONT_RED;

	/** @var string Font color for warning titles */
	protected $fontTitleError = RCli::FONT_RED;

	/** @var string|false Log warnings to this category or disable logging if false */
	protected $warningLogCategory = 'application.console.warning';

	/** @var string|false Log errors to this category or disable logging if false */
	protected $errorLogCategory = 'application.console.error';

	/** @var array List of warnings during script execution */
	private $_warnings = [];

	/**
	 * Console application error
	 *
	 * @param string $msg Error message text
	 * @param bool $stopExecution
	 */
	public function error($msg, $stopExecution = true) {
		if ($this->errorLogCategory === false) {
			\Yii::log($msg, \CLogger::LEVEL_ERROR, $this->errorLogCategory);
		}

		$this->msg("ERROR: ", $this->fontTitleError);
		$this->line($msg, $this->fontColorError);
		if ($stopExecution) {
			\Yii::app()->end();
		}
	}

	/**
	 * Console application waring
	 * Does not stop script execution
	 *
	 * @param string $msg Warning message
	 * @param bool $showInstantly Show message in moment of method call
	 */
	public function warning($msg, $showInstantly = true) {
		if ($this->warningLogCategory === false) {
			\Yii::log($msg, \CLogger::LEVEL_WARNING, $this->warningLogCategory);
		}

		if ($showInstantly) {
			$this->msg("WARNING: ", $this->fontTitleWarning);
			$this->line($msg, $this->fontColorWarning);
		}
		$this->_warnings[] = $msg;
	}

	/**
	 * Get list of warnings, occurred during script execution
	 * @return void
	 */
	public function listWarnings() {
		if (count($this->_warnings) > 0) {
			$this->hr('=', $this->fontTitleWarning);
			$this->line(sprintf("Warnings (%d):", count($this->_warnings)), $this->fontTitleWarning);
			$this->eol();
			foreach ($this->_warnings as $warning) {
				$this->line($warning, $this->fontColorWarning);
			}
			$this->eol();
		}
	}

	/**
	 * Get list of warnings
	 * @return array
	 */
	public function getWarnings() {
		return $this->_warnings;
	}
}