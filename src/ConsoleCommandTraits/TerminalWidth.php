<?php
/**
 * @author: Koshkin Alexey <koshkin.alexey@gmail.com>
 */

namespace ReachCli\ConsoleCommandTraits;

use ReachCli\RCli;

/**
 * Trait TerminalWidth
 * Functions for building console application that adopts to terminal size
 *
 * @package ReachCli
 * @subpackage ConsoleCommandTraits
 */
trait TerminalWidth
{
	/** @var int Default number of columns (terminal width)  */
	protected $terminalNumColsDefault = 80;

	/** @var int Default number of rows (terminal height) */
	protected $terminalNumRowsDefault = 30;

	/** @var int|null Real number of columns in active terminal (or false if unknown) */
	private $_terminalNumCols = null;

	/** @var int|null Real number of rows in active terminal (or false if unknown) */
	private $_terminalNumRows = null;

	/**
	 * Get width of current terminal (number of columns)
	 * @return int|null
	 */
	public function getTerminalWidth() {

		if ($this->_terminalNumCols === null) {
			$this->detectTerminalSize();
		}

		return $this->_terminalNumCols
			? $this->_terminalNumCols
			: $this->terminalNumColsDefault;
	}

	/**
	 * Get height of current terminal (number of columns)
	 * @return int|null
	 */
	public function getTerminalHeight() {
		if ($this->_terminalNumCols === null) {
			$this->detectTerminalSize();
		}

		return $this->_terminalNumCols
			? $this->_terminalNumCols
			: $this->terminalNumColsDefault;
	}

	/**
	 * Detect terminal size
	 * @return void
	 */
	protected function detectTerminalSize() {
		list ($cols, $rows) = \ReachCli\TerminalInfo::getSize();

		$this->_terminalNumCols = false;
		$this->_terminalNumRows = false;

		if ($cols) {
			$this->_terminalNumCols = $cols;
			RCli::$lineWidth = $cols;
		}

		if ($rows) {
			$this->_terminalNumRows = $rows;
		}
	}

}