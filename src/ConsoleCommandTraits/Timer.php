<?php
/**
 * @author: Koshkin Alexey <koshkin.alexey@gmail.com>
 */

namespace ReachCli\ConsoleCommandTraits;

/**
 * Class Timer
 * @package ReachCli\ConsoleCommandTraits
 */
trait Timer
{
	/** @var int Script begin execution timestamp. Used for statistic. */
	private $_executionTime = 0;

	/** @var int User executable timer  */
	private $_timerStart;

	/**
	 * Start script execution
	 * @return void
	 */
	protected function startExecutionTime()
	{
		$this->_executionTime = microtime(true);
	}

	/**
	 * Get current script execution time in microseconds
	 * @return mixed
	 */
	protected function getExecutionTime() {
		return microtime(true) - $this->_executionTime;
	}

	/**
	 * Reset and start user timer
	 * @return void
	 */
	protected function beginTimer()
	{
		$this->_timerStart = microtime(true);
	}

	/**
	 * Stop user timer and return its value in microseconds
	 * @return float
	 */
	protected function stopTimer()
	{
		$t = microtime(true) - $this->_timerStart;
		$this->_timerStart = null;
		return $t;
	}

}