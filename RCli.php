<?php
/**
 * @author: Koshkin Alexey <koshkin.alexey@gmail.com>
 */

/**
 * Class ReachCli
 *
 * Helper for colorful and reach text formatting for console output
 */
class RCli {

	/** Text colours */
	const CODE_FONT_BLACK = 30; // Black
	const CODE_FONT_RED = 31; // Red
	const CODE_FONT_GREEN = 32; // Green
	const CODE_FONT_YELLOW = 33; // Yellow
	const CODE_FONT_BLUE = 34; // Blue
	const CODE_FONT_MAGENTA = 35; // Magenta
	const CODE_FONT_CYAN = 36; // Cyan
	const CODE_FONT_WHITE = 37; // White

	/** Background colours */
	const CODE_BG_BLACK = 40; // Black
	const CODE_BG_RED = 41; // Red
	const CODE_BG_GREEN = 42; // Green
	const CODE_BG_YELLOW = 43; // Yellow
	const CODE_BG_BLUE = 44; // Blue
	const CODE_BG_MAGENTA = 45; // Magenta
	const CODE_BG_CYAN = 46; // Cyan
	const CODE_BG_WHITE = 47; // White

	/** Display styles */
	const CODE_DEFAULT = 0; // Reset to default value
	const CODE_BRIGHT_MORE = 1; // Set text more brighter
	const CODE_BRIGHT_LESS = 2; // Set text less brighter
	const CODE_BRIGHT_NORMAL = 5; // Normal brightness
	const CODE_UNDERLINE = 4; // Underlined test
	const CODE_INVERT = 7; // Invert foreground and background
	const CODE_HIDE = 8; // Hidden text

	/** @var string End of line symbol*/
	public static $eol = PHP_EOL;

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
			$codeString = join(';', $codes);
		} else {
			$codeString = $codes;
		}

		return
			self::writeCode($codeString)
			. $string
			. self::writeCode(self::CODE_DEFAULT);
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
	 * Horizontal line
	 *
	 * @param string $char
	 * @param int $codes
	 * @return string
	 */
	public static function hr($char = '=', $codes = self::CODE_FONT_BLACK)
	{
		return self::writeString(str_repeat($char, (int)self::$lineWidth), $codes) . self::$eol;
	}

}