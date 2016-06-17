<?php
/**
 * @author: Koshkin Alexey <koshkin.alexey@gmail.com>
 */

namespace ReachCli\ConsoleCommandTraits;

/**
 * Trait AllTogether
 * All ConsoleCommandTraits joined together
 *
 * @package ReachCli
 * @subpackage ConsoleCommandTraits
 */
trait AllTogether
{
	use ErrorWarning;
	use Help;
	use ListSelect;
	use Statistic;
	use TerminalWidth;
	use Timer;
	use Transliteration;
}