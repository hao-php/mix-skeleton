<?php

namespace App\Lib\Cli;

use Mix\Cli\Argv;
use Mix\Cli\Exception\NotFoundException;
use Mix\Cli\Flag;

/**
 * Class Application
 * @package Mix\Cli
 */
class Application extends \Mix\Cli\Application
{

    /**
     * @var array [][]
     */
    protected $arrCommands = [];


    /**
     * @return $this
     */
    public function addArrCommand(array ...$commands): Application
    {
        foreach ($commands as $cmd) {
            if (isset($this->arrCommands[$cmd['name']])) {
                throw new \RuntimeException('重复的命令');
            }
            $this->arrCommands[$cmd['name']] = $cmd;
        }
        return $this;
    }

    /**
     * Run
     */
    public function run(): void
    {
        if (PHP_SAPI != 'cli') {
            throw new \RuntimeException('Please run in cli mode.');
        }
        if (count($this->arrCommands) == 0) {
            throw new \RuntimeException('Command cannot be empty');
        }

        try {
            if (Argv::command() == '') {
                if (Flag::match('h', 'help')->bool()) {
                    $this->globalHelp();
                    return;
                }
                if (Flag::match('v', 'version')->bool()) {
                    $this->version();
                    return;
                }
                $options = Flag::options();
                if (empty($options)) {
                    $this->globalHelp();
                    return;
                } elseif ($this->singleton) {
                    $this->call();
                    return;
                }
                $keys = array_keys($options);
                $flag = array_shift($keys);
                $script = Argv::program()->path;
                throw new NotFoundException("flag provided but not defined: '{$flag}', see '{$script} --help'."); // 这里只是全局flag效验
            }
            if (Argv::command() !== '' && Flag::match('help')->bool()) {
                $this->arrCommandHelp();
                return;
            }
            $this->call();
        } catch (NotFoundException $ex) {
            static::println($ex->getMessage());
        }
    }

    protected function globalHelp(): void
    {
        $script = Argv::program()->path;
        static::println("Usage: {$script}" . ($this->singleton ? '' : ' [OPTIONS] COMMAND') . " [ARG...]");
        $this->printArrCommands();
        $this->printGlobalOptions();
        static::println('');
        static::println("Run '{$script}" . ($this->singleton ? '' : ' COMMAND') . " --help' for more information on a command.");
        static::println('');
        static::println("Developed with Mix PHP framework. (openmix.org/mix-php)");
    }

    protected function arrCommandHelp(): void
    {
        $script = Argv::program()->path;
        $command = Argv::command();
        $cmd = $this->getArrCommand($command);
        if (!$cmd) {
            return;
        }
        $this->printCommandOptions();
        static::println('');
        static::println("Developed with Mix PHP framework. (openmix.org/mix-php)");
    }

    protected function printGlobalOptions(): void
    {
        $tabs = "\t";
        static::println('');
        static::println('Global Options:');
        static::println("  -h, --help{$tabs}Print usage");
        static::println("  -v, --version{$tabs}Print version information");
    }

    protected function printArrCommands(): void
    {
        static::println('');
        static::println('Commands:');
        foreach ($this->arrCommands as $command) {
            $name = $command['name'];
            $short = $command['short'];
            static::println("  {$name}\t{$short}");
        }
    }

    protected function printCommandOptions(): void
    {
        $cmd = $this->getArrCommand(Argv::command());
        if (!$cmd) {
            return;
        }
        $cmdHandler = $this->makeCmdHandler($cmd);
        $options = $cmdHandler->options;
        if (empty($options)) {
            return;
        }
        static::println('');
        static::println('Command Options:');
        foreach ($options as $option) {
            $flags = [];
            foreach ($option->names as $name) {
                if (strlen($name) == 1) {
                    $flags[] = "-{$name}";
                } else {
                    $flags[] = "--{$name}";
                }
            }
            $flag = implode(', ', $flags);
            $usage = $option->usage;
            static::println("  {$flag}\t{$usage}");
        }
    }

    /**
     * @param string $command
     * @return array|null
     */
    protected function getArrCommand(string $command)
    {
        if (!isset($this->arrCommands[$command])) {
            throw new \RuntimeException($command . '命令不存在');
        }
        return $this->arrCommands[$command];
    }

    protected function arrValidateOptions(BaseRun $cmdHandler): void
    {
        $options = $cmdHandler->options;
        $flags = [];
        foreach ($options as $option) {
            foreach ($option->names as $name) {
                if (strlen($name) == 1) {
                    $flags[] = "-{$name}";
                } else {
                    $flags[] = "--{$name}";
                }
            }
        }
        foreach (array_keys(Flag::options()) as $flag) {
            if (!in_array($flag, $flags)) {
                $script = Argv::program()->path;
                $command = Argv::command();
                $command = $command ? " {$command}" : $command;
                throw new NotFoundException("flag provided but not defined: '{$flag}', see '{$script}{$command} --help'.");
            }
        }
    }

    protected function makeCmdHandler(array $cmd): BaseRun
    {
        if (empty($cmd['class'])) {
            throw new \RuntimeException('class is empty');
        }
        $class = $cmd['class'];
        if (!class_exists($class)) {
            throw new NotFoundException("'{$class}' is not exists");
        }

        return new $class;
    }

    protected function call(): void
    {
        $command = Argv::command();
        $cmd = $this->getArrCommand($command);
        if (!$cmd) {
            $script = Argv::program()->path;
            throw new NotFoundException("'{$command}' is not command, see '{$script} --help'.");
        }

        $cmdHandler = $this->makeCmdHandler($cmd);

        $this->arrValidateOptions($cmdHandler);


        $cmdHandler->main();
    }

    /**
     * @param $string
     */
    protected static function println($string)
    {
        printf("%s\n", $string);
    }

}
