<?php
/**
 * @author: Koshkin Alexey <koshkin.alexey@gmail.com>
 */

namespace ReachCli\ConsoleCommandTraits;

use ReachCli\RCli;

/**
 * Trait ListSelect
 * Ask user to select one of predefined answers for question
 *
 * @package ReachCli
 * @subpackage ConsoleCommandTraits
 */
trait ListSelect
{
	/** @var string Default message to ask user */
	protected $messageListSelectDefaultQuestion = 'Select one of list';

	/** @var string Label that masks default answer */
	protected $labelListSelectDefaultAnswer = ' <== default';

	/** @var string Font color for question that user is asked */
	protected $colorListSelectQuestion = RCli::FONT_YELLOW;

	/** @var string Font color for answer number in list of possible answers */
	protected $colorListSelectNumber = RCli::FONT_GREEN;

	/** @var string Font color for answer text in list of possible answers */
	protected $colorListSelectValue = RCli::FONT_WHITE;

	/** @var string Font color for "default" label, that marks default answer */
	protected $colorListSelectDefaultLabel = RCli::FONT_BLUE;

	/** @var string Font color for separating line */
	protected $colorLine = [RCli::FONT_WHITE, RCli::BRIGHT_LESS];

	/**
	 * Ask user to choose one element list
	 *
	 * @param array $list Array with possible values
	 * @param string|false $msg Prompt message of false if no message should be displayed
	 * @param mixed|null $defaultValue
	 * @param bool $returnKey If true key of selected array element is returned, it's value is returned otherwise
	 * @return mixed
	 */
	public function listSelect($list, $msg = null, $defaultValue = null, $returnKey = false)
	{
		$i = 0;
		$searchTargetArray = [];
		$selected = null;

		if ($msg === null) $msg = $this->messageListSelectDefaultQuestion;

		$this->hr('-', $this->colorLine);
		foreach ($list as $key=>$value) {
			$i++;
			$this->msg(sprintf("%2d) ", $i), $this->colorListSelectNumber);
			$this->msg(sprintf("%s", $value), $this->colorListSelectValue);

			if (null !== $defaultValue && (($value === $defaultValue && false == $returnKey) || ($key === $defaultValue && true == $returnKey))) {
				$this->msg($this->labelListSelectDefaultAnswer,$this->colorListSelectDefaultLabel);
			}

			$this->eol();

			$searchTargetArray[$i] = $returnKey ? $key : $value;
		}

		do {
			if ($msg !== false) $this->line($msg, $this->colorListSelectQuestion);

			$input = trim(fgets(STDIN));
			if (false !== array_search($input, array_keys($searchTargetArray))) {
				$selected = $input;
			} elseif (null !== $defaultValue && false !== ($key = array_search($defaultValue, $searchTargetArray))) {
				$selected = $key;
			}
		} while (empty($selected));

		return $searchTargetArray[$selected];
	}
}