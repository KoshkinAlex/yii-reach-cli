<?php
/**
 * @author: Koshkin Alexey <koshkin.alexey@gmail.com>
 */

namespace ReachCli;

/**
 * Class RCli
 *
 * Helper for colorful and reach text formatting in console output
 *
 * @package ReachCli
 */
class RCli {

	/** Font colours */
	const FONT_BLACK 	= 30; 	// Black
	const FONT_RED 		= 31; 	// Red
	const FONT_GREEN 	= 32; 	// Green
	const FONT_YELLOW 	= 33; 	// Yellow
	const FONT_BLUE 	= 34; 	// Blue
	const FONT_MAGENTA 	= 35; 	// Magenta
	const FONT_CYAN 	= 36; 	// Cyan
	const FONT_WHITE 	= 37; 	// White

	/** Background colours */
	const BG_BLACK 		= 40; 	// Black
	const BG_RED 		= 41; 	// Red
	const BG_GREEN 		= 42; 	// Green
	const BG_YELLOW 	= 43; 	// Yellow
	const BG_BLUE 		= 44; 	// Blue
	const BG_MAGENTA 	= 45; 	// Magenta
	const BG_CYAN 		= 46; 	// Cyan
	const BG_WHITE 		= 47; 	// White

	/** Display styles */
	const CLEAR 		= 0; 	// Reset to default value
	const BRIGHT_MORE 	= 1; 	// Set text more brighter
	const BRIGHT_LESS 	= 2; 	// Set text less brighter
	const BRIGHT_NORMAL = 5; 	// Normal brightness
	const UNDERLINE 	= 4; 	// Underlined test
	const INVERT 		= 7; 	// Invert foreground and background
	const HIDE 			= 8; 	// Hidden text

	const JOIN_CODE = ';';

	/** @var int Length of line */
	public static $lineWidth = 80;

	/**
	 * Apply string using one or more format codes
	 *
	 * @param string $string Text to apply formatting
	 * @param mixed $codes Appearance codes
	 * @return string Formatted and wrapped with control sequences string
	 */
	public static function writeString($string, $codes)
	{
		if (is_array($codes)) {
			$codeString = join(static::JOIN_CODE, $codes);
		} else {
			$codeString = $codes;
		}

		return
			self::writeCode($codeString)
			. $string
			. self::writeCode(self::CLEAR);
	}

	/**
	 * Write one control sequence item
	 *
	 * @param $code integer
	 * @return string
	 */
	public static function writeCode($code)
	{
		return "\033[{$code}m";
	}

	/**
	 * Horizontal line with line ending
	 *
	 * @param string $char
	 * @param int $codes
	 * @return string
	 */
	public static function hr($char = '=', $codes = self::FONT_BLACK)
	{
		return self::writeString(str_repeat($char, (int)self::$lineWidth), $codes) . PHP_EOL;
	}

}