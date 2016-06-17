<?php
/**
 * @author: Koshkin Alexey <koshkin.alexey@gmail.com>
 */

namespace ReachCli\Examples;
use ReachCli\RCli;

/**
 * Class AllExampleCommand
 * Examples of all all of the functionality
 *
 * @package ReachCli
 * @subpackage Examples
 */
class AllExampleCommand extends \ReachCli\ConsoleCommand
{
	/**
	 * All actions, combined to one
	 */
	public function actionAll() {

		$this->line('Basic examples of reach console output');
		$this->actionBasic();

		$this->line('Ask user for question with binary answer');
		$this->actionConfirm();

		$this->line('Ask user for question with custom answer');
		$this->actionPrompt();

		$this->line('Ask user for one of predefined answers');
		$this->actionSelect();

		$this->line('Remember some command statistic and show it at the end of script execution');
		$this->actionStatistic();

		$this->line('Sample warning and error messages');
		$this->actionWarning();
	}

	/**
	 * Basic examples of reach console output
	 */
	public function actionBasic() {

		// Simple colour messages
		$this->msg('RED', RCli::FONT_RED);
		$this->msg('GREEN', RCli::FONT_GREEN);
		$this->msg('BLUE', RCli::FONT_BLUE);
		$this->eol();

		// Other messages
		$this->hr();
		$this->line('Too many color line ', [RCli::FONT_CYAN, RCli::BG_MAGENTA, RCli::UNDERLINE]);
		$this->hr('*', RCli::FONT_YELLOW);
		$this->line(sprintf('Many %s combined %s one line', RCli::msg('colors', RCli::FONT_RED), RCli::msg('into', RCli::FONT_GREEN)), RCli::FONT_YELLOW);
		$this->hr('=', [RCli::FONT_WHITE, RCli::BRIGHT_LESS]);

		// Status
		$this->status('Good news', true);
		$this->status('Bad news', false);
		$this->status('Status with some value', true, 300);
	}

	/**
	 * Ask user for question with binary answer
	 */
	public function actionConfirm() {
		$userReply = $this->confirm("Do you want to launch rocket to Mars?", RCli::FONT_YELLOW);
		$this->status('Martian mission starting', $userReply);
	}

	/**
	 * Ask user for question with custom answer
	 */
	public function actionPrompt() {
		$userReply = $this->prompt("Please enter new password", RCli::FONT_BLUE);
		$this->line(sprintf('Sorry, password %s is used by user %s, choose other one', RCli::msg($userReply, RCli::FONT_RED), RCli::msg('admin', RCli::FONT_YELLOW)));
	}

	/**
	 * Ask user for one of predefined answers
	 */
	public function actionSelect() {

		$answers = [ 'banana', 'apple', 'strawberry', 'stone',];

		do {
			$result = $this->listSelect($answers, 'Please select most tasteless thing', 'apple');
			$correct = ($result === 'stone');
			$this->status('You answered', $correct, $result);
		} while (!$correct);
	}

	/**
	 * Remember some command statistic and show it at the end of script execution
	 */
	public function actionStatistic() {
		for ($i = 1; $i <= rand(50, 100); $i+= rand(1,3)) {
			$this->inc('Total numbers');

			if ($i % 2 == 0) {
				$this->inc('Even numbers');
			} else {
				$this->inc('Odd numbers');
			}

			$this->inc('Overall sum', $i);
		}

		$this->printStat();
	}

	/**
	 * Sample warning and error messages
	 */
	public function actionWarning() {
		$this->warning('Please try one more time');
		$this->error('Thank you Mario, but your princess is in another castle!');
	}
}