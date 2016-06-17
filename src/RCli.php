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
	 * Apply string decoration using one or more format codes
	 *
	 * @param string $string Text to apply formatting
	 * @param mixed $codes Appearance codes
	 * @return string Formatted and wrapped with control sequences string
	 */
	public static function msg($string, $codes) {

		$codeString = is_array($codes)
			? join(static::JOIN_CODE, $codes)
			: $codes;

		return
			self::writeCode($codeString)
			. $string
			. self::writeCode(self::CLEAR);
	}

	/**
	 * The same as self::msg() but with line ending
	 *
	 * @param $string
	 * @param $codes
	 * @return string
	 */
	public static function line($string, $codes) {
		return self::msg($string, $codes) . PHP_EOL;
	}

	/**
	 * @deprecated
	 * @see self::msg()
	 * @param $string
	 * @param $codes
	 * @return string
	 */
	public static function writeString($string, $codes)
	{
		return self::msg($string, $codes);
	}

	/**
	 * Horizontal line with line ending
	 *
	 * @param string $char Symbol to construct horizontal line
	 * @param string|array|integer|null $codes Message decorate code(s)
	 * @return string
	 */
	public static function hr($char = '=', $codes = self::FONT_BLACK)
	{
		return self::line(str_repeat($char, (int)self::$lineWidth), $codes);
	}

	/**
	 * Header for some text, separated with horizontal lines
	 *
	 * @param $message
	 * @param string|array|integer|null $codes Message decorate code(s)
	 * @param string|array|integer|null $lineCodes Horizontal lines decorate code(s)
	 * @return string
	 */
	public static function header($message, $codes, $lineCodes = null)
	{
		return
			PHP_EOL
			. static::hr('=', $lineCodes)
			. static::line($message, $codes)
			. static::hr('-', $lineCodes);
	}

	/**
	 * Console output for not only scalar variables
	 * @param mixed $var
	 */
	public static function outVar($var)
	{
		$outRec = function($var, $level) use (&$outRec) {
			$out = '';
			if (is_array($var)) {
				foreach ($var as $key => $value) {
					$out .= self::msg(str_repeat('  ', $level).sprintf("%-40s",$key)."   ", [self::FONT_WHITE, self::BRIGHT_LESS]);
					$out .= $outRec($value, $level+1);
				}
			} elseif (method_exists($var, '__toString')) {
				$out .= static::line($var, self::FONT_YELLOW);
			} else {
				$out .= static::line(print_r($var, true), self::FONT_YELLOW);
			}

			return $out;
		};

		return $outRec($var, 0);
	}

	/**
	 * Generate table row
	 *
	 * @param array $data Table cells
	 * @param int $defaultWidth Default cell width (in chars)
	 * @param string|array|integer|null $defaultColor Colour for the whole row (by default)
	 * @return string
	 *
	 * Each cell can be defined in one of this ways:
	 * 		$value,
	 * 		[$value],
	 * 		[$value, $width],
	 * 		[$value, $width, $color],
	 */
	public static function tableRow($data, $defaultWidth = 10, $defaultColor = null) {
		$output = '';
		$num = 0;
		foreach ($data as $col) {
			if ($num == 0) $output .= self::msg('|',[self::FONT_WHITE, self::BRIGHT_LESS]);

			$value = is_array($col) && isset($col[0])
				? $col[0]
				: $col;

			$width = is_array($col) && isset($col[1])
				? intval($col[1])
				: $defaultWidth;

			$color = is_array($col) && !empty($col[2])
				? $col[2]
				: $defaultColor;

			$output .= self::msg(sprintf(" %{$width}s ",$value), $color);

			$num++;

			$output .= self::msg('|',[self::FONT_WHITE, self::BRIGHT_LESS]);
		}
		return $output . PHP_EOL;
	}

	/**
	 * Write one control sequence item
	 *
	 * @param $code integer
	 * @return string
	 */
	protected static function writeCode($code)
	{
		return "\033[{$code}m";
	}

}