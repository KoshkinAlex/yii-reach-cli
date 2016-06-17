<?php
/**
 * @author: Koshkin Alexey <koshkin.alexey@gmail.com>
 */

namespace ReachCli\Examples;

/**
 * Class TimerCommand
 * Basic functionality for timer trait
 *
 * @package ReachCli
 * @subpackage Examples
 */
class TimerCommand extends \CConsoleCommand
{
	use \ReachCli\ConsoleCommandTraits\Timer;

	/**
	 * We should call startExecutionTime() at the beginning of the script execution
	 * @param string $name
	 * @param \CConsoleCommandRunner $runner
	 */
	public function __construct($name, $runner)
	{
		parent::__construct($name, $runner);
		$this->startExecutionTime();
	}

	/**
	 * See timer functionality
	 */
	public function actionIndex() {
		for ($i = 1; $i <= 5; $i++) {
			$this->beginTimer();
			usleep($i * 1E5);
			printf ("Iteration %d: %.1f" . PHP_EOL, $i, $this->stopTimer());
		}
		printf ("Total script execution: %.1f sec". PHP_EOL, $this->getExecutionTime());
	}
}