<?php
/**
 * @author: Koshkin Alexey <koshkin.alexey@gmail.com>
 */

namespace ReachCli\Examples;

/**
 * Class HelpCommand
 * This command shows functionality of automatic help generation
 *
 * @package ReachCli
 * @subpackage Examples
 */
class HelpCommand extends \CConsoleCommand
{
	use \ReachCli\ConsoleCommandTraits\Help;

	/** @var string Set default command action to 'actionHelp' */
	public $defaultAction = 'help';

	/**
	 * Print message "One"
	 */
	public function actionOne() {
		print "One";
	}

	/**
	/**
	 * Print message
	 * @param string $message Message to print
	 */
	public function actionMessage($message) {
		print $message;
	}

	/**
	 * Print one message many times
	 * @param string $message Message to print
	 * @param int $repeat Times to repeat message
	 */
	public function actionRepeatMessage($message, $repeat = 3) {
		echo str_repeat($message, $repeat);
	}

}