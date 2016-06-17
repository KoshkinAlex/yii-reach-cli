<?php
/**
 * @author: Koshkin Alexey <koshkin.alexey@gmail.com>
 */

namespace ReachCli;

/**
 * Class TerminalInfo
 * Get information about current terminal session
 *
 * @package ReachCli
 */
class TerminalInfo
{
	/**
	 * Heuristic check that script is executed by human. Terminal sessions variables are serving as indicators.
	 *
	 * @return bool
	 */
	public static function isExecutedByHuman()
	{
		return

			//Linux
			isset($_SERVER['SSH_CLIENT'])
			|| isset($_SERVER['TERM'])
			|| isset($_SERVER['SSH_TTY'])
			|| isset($_SERVER['SSH_CONNECTION'])

			// Windows
			|| isset($_SERVER['HOMEPATH'])
			||  isset($_SERVER['USERNAME']);
	}

	/**
	 * Detect run on windows console
	 * @return bool
	 */
	public static function isWindowsConsole()
	{
		return
			isset($_SERVER['PATHEXT'])
			|| isset($_SERVER['windir']);
	}
}