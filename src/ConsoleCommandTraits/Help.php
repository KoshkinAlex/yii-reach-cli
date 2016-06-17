<?php
/**
 * @author: Koshkin Alexey <koshkin.alexey@gmail.com>
 */

namespace ReachCli\ConsoleCommandTraits;

use ReachCli\RCli;

/**
 * Class Help
 * Parse PHPDoc in ConsoleCommand and print command help, based on it
 *
 * @package ReachCli
 * @subpackage ConsoleCommandTraits
 */
trait Help
{
	/** @var string Set default command action to 'actionHelp' */
	public $defaultAction = 'help';

	/** @var string Font color for help block header */
	protected $colorHelpHeader = RCli::FONT_RED;

	/** @var string Font color for command name (which help is generated) */
	protected $colorHelpHeaderClassName = [RCli::FONT_RED, RCli::UNDERLINE];

	/** @var string Font color for command description */
	protected $colorHelpDescription = RCli::FONT_YELLOW;

	/** @var string Header label for list of command available actions */
	protected $labelHelpAvailableActions = "\tAvailable actions:";

	/** @var string Font color for $this->labelHelpAvailableActions */
	protected $colorHelpAvailableActions = RCli::UNDERLINE;

	/** @var string Font color for command action name */
	protected $colorHelpActionName = [RCli::FONT_RED, RCli::BRIGHT_MORE];

	/** @var string Font color for command action description */
	protected $colorHelpActionDescription = RCli::FONT_YELLOW;

	/** @var string Font color for action parameter name and value */
	protected $colorHelpActionParameter = RCli::FONT_BLUE;

	/** @var string Font color for action parameter description */
	protected $colorHelpActionParameterDescription = RCli::FONT_WHITE;

	/** @var string Label that marks default action in list of available command actions */
	protected $labelHelpDefaultAction = '[default]';

	/** @var string Font color for label than indicates default action */
	protected $colorHelpDefaultAction = RCli::FONT_GREEN;

	/**
	 * Default action - show command help
	 * @return void
	 */
	public function actionHelp(){
		$this->out($this->getHelp());
	}

	/**
	 * Get all available actions in command
	 *
	 * @return string
	 */
	public function getHelp()
	{
		$class = new \ReflectionClass(get_class($this));
		$help = RCli::hr('=', $this->colorHelpHeader)
				. ' '
				. RCli::msg( $class->getName(), $this->colorHelpHeaderClassName);

		$description = $this->getDocText($class->getDocComment());

		if ($description) {
			$help .= PHP_EOL . '	' . RCli::msg($description, $this->colorHelpDescription) . PHP_EOL;
		}
		$help .= PHP_EOL . RCli::msg($this->labelHelpAvailableActions, $this->colorHelpAvailableActions) . PHP_EOL . PHP_EOL;

		foreach ($class->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
			$name = $method->getName();
			if (!strncasecmp($name, 'action', 6) && strlen($name) > 6) {
				$actionName = substr($name, 6);
				$actionName[0] = strtolower($actionName[0]);
				$help .= "	" . RCli::msg($actionName, $this->colorHelpActionName);
				if ($this->defaultAction == $actionName) {
					$help .= ' ' . RCli::msg($this->labelHelpDefaultAction, $this->colorHelpDefaultAction) . ' ';
				}
				$docComment = $method->getDocComment();
				$description = $this->getDocText($docComment);

				if ($description) {
					$help .= " - " . RCli::msg($description, $this->colorHelpActionDescription);
				}
				$help .= PHP_EOL;

				foreach ($method->getParameters() as $param) {
					$defaultValue = $param->isDefaultValueAvailable() ? print_r($param->getDefaultValue(), 1) : null;
					$name = $param->getName();

					// Action parameter value
					$help .= RCli::msg("\t\t" . ($param->isOptional() ? "[--$name=$defaultValue]" : "--$name=value"), $this->colorHelpActionParameter );

					// Action parameter description
					if (preg_match("/{$name}\s*([^\@\*\n]+)[\@\*\n]/is", $docComment, $m)) {
						$help .= "\t" . RCli::msg($m[1], $this->colorHelpActionParameterDescription);
					}
				}
				$help .= PHP_EOL;
			}
		}
		return $help . PHP_EOL;
	}

	/**
	 * Get description text from PHPDoc comment
	 *
	 * @param string $doc Full PHPDoc comment
	 * @return string Only text description
	 */
	protected function getDocText($doc) {
		$description = [];
		foreach (explode("\n", explode('@', $doc, 2)[0]) as $line) {
			$line = trim($line, " \t\n\r\0\x0B/*.");
			if ($line) $description[] = $line.".";
		}

		return implode(' ', $description);
	}
}