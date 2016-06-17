yii-reach-cli
=============

Component for Yii 1.* framework that helps to make console application more
interactive and pretty for less time.

Requirements
------------

1. Yii framework >= 1.1.9
2. PHP >= 5.5

Usage
------------

Use \ReachCli\ConsoleCommand as a base class for all console commands to have all provided functionality at time.
Use traits from \ReachCli\ConsoleCommandTraits\* to add only selected abilities to your console application.

Examples
------------

In all examples **RCli** means **\ReachCli\RCli** class, and **$this** means
instance of object that extends **\ReachCli\ConsoleCommand** class.

## Output messages

```php
    // Message
    RCli::msg('Message text', RCli::FONT_RED)
    $this->msg('Message text', RCli::FONT_RED);

    // Message with line ending
    RCli::line('Message text', RCli::FONT_RED)
    $this->line('Message text', RCli::FONT_RED);

    // Horizontal line
    RCli::hr();
    $this->hr();
    RCli::hr('*', RCli::FONT_BLUE);

    // Header
    RCli::header('Message text');
    $this->header('Message text');
    $this->header('Message text', RCli::FONT_RED, RCli::FONT_BLUE);

    // Message with some status
    $this->status('Good news', true);
    $this->status('Bad news', false);
    $this->status('Status with some value', true, 300);

```

## Interact with user

```php
    // Ask user for question with binary answer
    $userReply = $this->confirm("Do you want to launch rocket to Mars?", RCli::FONT_YELLOW);

    // Ask user for question with custom answer
    $userReply = $this->prompt("Please enter new password", RCli::FONT_BLUE);

	// Ask user for one of predefined answers
    $answers = [ 'banana', 'apple', 'strawberry', 'stone',];
    $userReply = $this->listSelect($answers, 'Please select most tasteless thing', 'apple');

```

## Help for console command that is generated using PHPDoc annotations
Use trait **ReachCli\ConsoleCommandTraits\Help** to use this functionality separately.
See **ReachCli\Examples\HelpCommand** for example.

```php
    // Include trait
	use \ReachCli\ConsoleCommandTraits\Help;

	// Optional Set default action to 'help'
	public $defaultAction = 'help';

	// Or use this everywhere you want
	echo $this->getHelp();
```

Receive command, actions and parameters description, generated from command class structure and it's PHPDoc

```
 ReachCli\Examples\HelpCommand
 Class HelpCommand. This command shows functionality of automatic help generation.

	Available actions:

	message - Print message.
		--message=value

	repeatMessage - Print one message many times.
		--message=value	many times
		[--repeat=3]	Times to repeat message

	help [default]  - Default action - show command help.
```

## Errors and warnings
Use trait **ReachCli\ConsoleCommandTraits\ErrorWarning** to use this functionality separately.

```php
    $this->warning('Please try one more time');
    $this->error('Thank you Mario, but your princess is in another castle!');
```

## Count different events and prints statistic for this events
Use trait **ReachCli\ConsoleCommandTraits\Statistic** to use this functionality separately.

```php
    // Remember "Event 1"
    $this->inc('Event 1');
    // Remember "Event 2" for 3 times
    $this->inc('Event 2', 3);

    // Print statistic
    $this->printStat();
```

## Command execution timers
Use trait **ReachCli\ConsoleCommandTraits\Timer** to use this functionality separately.
See **ReachCli\Examples\TimerCommand** for example.

```php
    $this->beginTimer(); // Start timer
    // ... Do something, maybe iteration of some cycle
    print "Execution time" . $this->stopTimer(); // Get iteration time

    print "Command total execution time" . $this->getExecutionTime();
```


