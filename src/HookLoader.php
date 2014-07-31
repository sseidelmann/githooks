<?php
/**
 * Created by PhpStorm.
 * User: sseidelmann
 * Date: 31.07.14
 * Time: 10:33
 */


namespace GitHooks;

use Symfony\Component\Console\Output\ConsoleOutput;

class HookLoader {

    /**
     * Saves the argv Input from CLI.
     *
     * @var    array
     * @author Sebastian Seidelmann <sebastian.seidelmann@googlemail.com>
     */
    private $argvInput;

    /**
     * Saves the mode to run.
     *
     * @var    string
     * @author Sebastian Seidelmann <sebastian.seidelmann@googlemail.com>
     */
    private $mode;

    /**
     * Saves the method to run.
     *
     * @var    string
     * @author Sebastian Seidelmann <sebastian.seidelmann@googlemail.com>
     */
    private $method;

    /**
     * Saves the config.
     *
     * @var    string
     * @author Sebastian Seidelmann <sebastian.seidelmann@googlemail.com>
     */
    private $config;

    /**
     * Saves the hooks.
     *
     * @var    AbstractHook[]
     * @author Sebastian Seidelmann <sebastian.seidelmann@googlemail.com>
     */
    private $hooks;

    /**
     * Constructs the hook loader.
     *
     * @param array $argvInput The input array from CLI
     *
     * @author Sebastian Seidelmann <sebastian.seidelmann@googlemail.com>
     * @return void
     */
    public function __construct(array $argvInput = array()) {
        $this->argvInput = $argvInput;
        $this->config    = $this->readConfig();
    }

    /**
     * Runs the hook itself.
     *
     * @author Sebastian Seidelmann <sebastian.seidelmann@googlemail.com>
     * @return void
     */
    public function run() {
        $this->debug();
        $this->parseInputOptions();


        switch ($this->mode) {
            case "check":
                $this->check();
                break;
            default:
                return false;
        }
    }

    /**
     * Runs the check.
     *
     * @author Sebastian Seidelmann <sebastian.seidelmann@googlemail.com>
     * @return void
     */
    private function check() {
        if (!$this->method) {
            $this->debug('method not given', 2);
            return;
        }

        $hooks = $this->config['hooks'];
        \GitHooks\Helper\ConsoleOutput::logger()->debug('searching available hooks for ' . $this->method);
        if (isset($hooks[$this->method])) {
            $hook = $hooks[$this->method];
            foreach ($hook as $class => $hookConfig) {
                $this->addHook(new $class($hookConfig));
            }
        }

        $files = $this->getFiles();
        print_r($files);

        \GitHooks\Helper\ConsoleOutput::logger()->write('');

        if ($this->getHooks() > 0) {
            foreach ($this->getHooks() as $hook) {
                \GitHooks\Helper\ConsoleOutput::logger()->debug('running ' . $hook->getName(), false);
                if ($hook->run()) {
                    \GitHooks\Helper\ConsoleOutput::logger()->write('  <fg=green;options=bold>√</fg=green;options=bold>');
                } else {
                    \GitHooks\Helper\ConsoleOutput::logger()->write('  <fg=red;options=bold>†</fg=red;options=bold>');
                }

            }
        }
    }

    /**
     * Get the files.
     *
     * @author Sebastian Seidelmann <sebastian.seidelmann@twt.de>
     * @return array
     */
    private function getFiles() {
        exec("git rev-parse --verify HEAD 2> /dev/null", $set, $return);

        $against = $return === 0
            ? 'HEAD'
            // or: diff against an empty tree object
            : '4b825dc642cb6eb9a060e54bf8d69288fbee4904';

        exec("git diff-index --cached --full-index {$against}", $files);


        return $files;
    }

    /**
     * @param AbstractHook $instance
     * @param $priority
     *
     * @author Sebastian Seidelmann <sebastian.seidelmann@googlemail.com>
     * @return void
     */
    private function addHook($instance, $priority = null) {
        if (null === $priority) {
            $priority = $instance->getPriority();
        }

        if (isset($this->hooks[$priority])) {
            $this->addHook($instance, $priority+1);
        } else {
            $this->hooks[$priority] = $instance;
        }
    }

    /**
     * Returns the array of hooks.
     *
     * @author Sebastian Seidelmann <sebastian.seidelmann@googlemail.com>
     * @return AbstractHook[]
     */
    private function getHooks() {
        ksort($this->hooks);
        return $this->hooks;
    }

    /**
     * Reads the config.
     *
     * @return mixed
     * @author Sebastian Seidelmann <sebastian.seidelmann@twt.de>
     */
    private function readConfig() {

        $path = realpath(dirname(__FILE__) . '/../') . DIRECTORY_SEPARATOR . 'config.json';
        if (!file_exists($path)) {
            $path = realpath(dirname(__FILE__) . '/../../../../') . DIRECTORY_SEPARATOR . 'config.json';
        }

        $json = file_get_contents($path);

        return json_decode($json,true);
    }

    /**
     * Sets the mode.
     *
     * @param string $mode the mode.
     *
     * @author Sebastian Seidelmann <sebastian.seidelmann@googlemail.com>
     * @return void
     */
    private function setMode($mode) {
        $this->mode = $mode;
    }

    /**
     * Sets the mode.
     *
     * @param string $mode the mode.
     *
     * @author Sebastian Seidelmann <sebastian.seidelmann@googlemail.com>
     * @return void
     */
    private function setMethod($method) {
        $this->method = $method;
    }

    /**
     * Parses the input options.
     *
     * @author Sebastian Seidelmann <sebastian.seidelmann@googlemail.com>
     * @return void
     */
    private function parseInputOptions() {
        if (isset($this->argvInput[1])) {
            $this->setMode($this->argvInput[1]);
            if (isset($this->argvInput[2])) {
                $this->setMethod($this->argvInput[2]);
            }
        }
    }

    private function debug($message = null, $mode = 1) {
        if (null === $message) {
            echo PHP_EOL;
        } else {
            $output = new ConsoleOutput();
            if ($mode == 2) {
                $output->writeln(sprintf(
                    '<fg=red;options=bold>==></fg=red;options=bold> <fg=white;options=bold>%s</fg=white;options=bold>',
                    $message
                ));
            } else {
                $output->writeln(sprintf(
                    '<fg=blue;options=bold>==></fg=blue;options=bold> <fg=white;options=bold>%s</fg=white;options=bold>',
                    $message
                ));
            }
        }
    }
}