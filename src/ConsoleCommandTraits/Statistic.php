<?php
/**
 * @author: Koshkin Alexey <koshkin.alexey@gmail.com>
 */

namespace ReachCli\ConsoleCommandTraits;

use ReachCli\RCli;

/**
 * Trait Statistic
 * Counts different events and prints statistic for this events
 *
 * @package ReachCli
 * @subpackage ConsoleCommandTraits
 */
trait Statistic
{
	/** @var string Font color for statistic header */
	protected $fontColorStatisticHeader = RCli::FONT_BLUE;

	/** @var string Font color for statistic parameter name */
	protected $fontColorStatisticLabel = RCli::FONT_YELLOW;

	/** @var string Font color for statistic parameter value */
	protected $fontColorStatisticValue = RCli::FONT_YELLOW;

	/** @var array Mapping of counter keys to human-readable indicator labels */
	private $_counterLabel = [];

	/** @var array Values of counter keys */
	private $_counter = [];

	/**
	 * Increment value of parameter that is interesting for us in final statistic
	 *
	 * @param string|bool $key Human friendly label of parameter (must be exactly the same in all calls)
	 * @param integer $count Increment step
	 * @return integer Parameter value after increment
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
		/** @var \ReachCli\ConsoleCommand $this */
		if (count($this->_counter) == 0) {
			return;
		}

		$this->eol();
		$this->hr('=', $this->fontColorStatisticHeader);
		$this->line("Script execution statistic", $this->fontColorStatisticHeader);

		/**
		 * If timer is enabled
		 * @see ReachCli\ConsoleCommandTraits\Timer
		 */
		if (method_exists($this, 'getExecutionTime')) {
			$this->printStatisticRow("Script execution time", sprintf("%.1f sec", $this->getExecutionTime()));
		}

		if ($this->hasOutput()) {
			foreach ($this->_counter as $key => $value) {
				$label = empty($this->_counterLabel[$key]) ? "Number of records" : $this->_counterLabel[$key];
				$this->printStatisticRow($label, $value);
			}
		}

		$this->eol(2);
	}

	/**
	 * One row for statistic output
	 *
	 * @param string $label Name of parameter
	 * @param string $value Parameter value
	 */
	protected function printStatisticRow($label, $value) {
		$this->msg("   " . $label . ": ", $this->fontColorStatisticLabel);
		$this->line($value, $this->fontColorStatisticValue);
	}
}