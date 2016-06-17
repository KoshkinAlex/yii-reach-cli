<?php
/**
 * @author: Koshkin Alexey <koshkin.alexey@gmail.com>
 */

namespace ReachCli\ConsoleCommandTraits;

/**
 * Class Transliteration
 * Possibility to transliterate messages before output
 *
 * @package ReachCli
 * @subpackage ConsoleCommandTraits
 */
trait Transliteration
{

	/** @var bool If is terminal needs to replace some characters */
	public $useTransliteration = false;

	/** @var array Transliteration table used if $this->useTransliteration is true */
	public $transliterationTable = [ 'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd', 'е' => 'e', 'ё' => 'yo', 'ж' => 'zh', 'з' => 'z', 'и' => 'i', 'й' => 'yi', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n', 'о' => 'o', 'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't', 'у' => 'u', 'ф' => 'f', 'х' => 'x', 'ц' => 'c', 'ч' => 'ch', 'ш' => 'sh', 'щ' => 'sh', 'ъ' => '', 'ы' => 'y', 'ь' => '', 'э' => 'e', 'ю' => 'yu', 'я' => 'ya', 'А' => 'a', 'Б' => 'b', 'В' => 'v', 'Г' => 'g', 'Д' => 'd', 'Е' => 'e', 'Ё' => 'yo', 'Ж' => 'zh', 'З' => 'z', 'И' => 'i', 'Й' => 'yi', 'К' => 'k', 'Л' => 'l', 'М' => 'm', 'Н' => 'n', 'О' => 'o', 'П' => 'p', 'Р' => 'r', 'С' => 's', 'Т' => 't', 'У' => 'u', 'Ф' => 'f', 'Х' => 'x', 'Ц' => 'c', 'Ч' => 'ch', 'Ш' => 'sh', 'Щ' => 'sh', 'Ъ' => '', 'Ы' => 'y', 'Ь' => '', 'Э' => 'e', 'Ю' => 'yu', 'Я' => 'ya',];

	/** @var null|bool Transliteration enable flag */
	private $_transliteration = null;

	/**
	 * Transliterate message if transliteration is enabled
	 * @param $message
	 * @return string
	 */
	protected function transliterate($message) {

		return $this->isTransliterationEnabled()
			? strtr($message, $this->transliterationTable)
			: $message;
	}

	/**
	 * Enable or disable transliteration in console output
	 * @param $status
	 */
	protected function setTransliteration($status) {
		$this->_transliteration = (bool) $status;
	}

	/**
	 * Autodetect if we need transliteration
	 */
	protected function autodetectTransliteration() {
		$this->setTransliteration((bool) \ReachCli\TerminalInfo::isWindowsConsole());
	}

	/**
	 * Check if transliteration is enabled
	 * @return bool
	 */
	private function isTransliterationEnabled() {
		if ($this->_transliteration === null) {
			$this->autodetectTransliteration();
		}

		return (bool) $this->_transliteration;
	}
}