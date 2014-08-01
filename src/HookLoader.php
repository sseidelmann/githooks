<?php
/**
 * Created by PhpStorm.
 * User: sseidelmann
 * Date: 31.07.14
 * Time: 10:33
 */


namespace GitHooks;

use GitHooks\Helper\GitFile;
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
        $this->argvInput = array_merge($argvInput, explode(' ', file_get_contents('php://stdin')));
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


        $files = $this->getFiles();


        $hooks = $this->config['hooks'];
        \GitHooks\Helper\ConsoleOutput::logger()->debug('searching available hooks for ' . $this->method);
        if (isset($hooks[$this->method])) {
            $hook = $hooks[$this->method];
            foreach ($hook as $class => $hookConfig) {

                /* @var $hookInstance AbstractHook */
                $hookInstance = new $class($hookConfig);
                $hookInstance->setFiles($files);

                $this->addHook($hookInstance);
            }
        }

        \GitHooks\Helper\ConsoleOutput::logger()->write('');

        $errors = array();
        if ($this->getHooks() > 0) {
            foreach ($this->getHooks() as $hook) {
                $return = $hook->run();
                $errors = array_merge($errors, $hook->getErrors());
            }
        }

        if (count($errors) > 0) {
            echo "----------------------------------" . PHP_EOL;
            print_r($errors);
            echo "----------------------------------" . PHP_EOL;
        }
    }

    private function execute($command, $displayDebugOutput = true) {

        echo PHP_EOL . "~~~~~~~~~~~~~~~~~~ COMMAND ~~~~~~~~~~~~~~~~~~" . PHP_EOL;
        echo " ~ '" . $command . "'" . PHP_EOL;
        $result = exec($command, $output, $return);

        $returnObject = (object) array(
            'result' => $result,
            'output' => $output,
            'return' => $return
        );

        if ($displayDebugOutput) {
            echo "   | result: " . $result . PHP_EOL;
            echo "   | return: " . $return . PHP_EOL;
            foreach ($output as $index => $line) {
                $out = "        ";
                if ($index == 0) {
                    $out = "output: ";
                }
                echo "   | " . $out . $line . PHP_EOL;
            }
            echo PHP_EOL;
            echo PHP_EOL;
        }

        return $returnObject;
    }

    /**
     * Returns the base sha1
     *
     * @return mixed
     * @author Sebastian Seidelmann <sebastian.seidelmann@twt.de>
     *
     */
    private function getOldRef() {
        return $this->argvInput[3];
    }

    /**
     * Returns the base sha1
     *
     * @return mixed
     * @author Sebastian Seidelmann <sebastian.seidelmann@twt.de>
     *
     */
    private function getNewRef() {
        return $this->argvInput[4];
    }

    /**
     * Returns the base sha1
     *
     * @return mixed
     * @author Sebastian Seidelmann <sebastian.seidelmann@twt.de>
     *
     */
    private function getRefName() {
        return trim($this->argvInput[5]);
    }


    /**
     * Get the files.
     *
     * @author Sebastian Seidelmann <sebastian.seidelmann@twt.de>
     * @return GitFile[]
     */
    private function getFiles() {

        // $this->execute(sprintf('git cat-file -p %s^{tree}', trim($this->argvInput[5])));
        // $this->execute(sprintf('git rev-parse --verify HEAD 2> /dev/null', trim($this->argvInput[5])));



        $gitDiffResult = $this->execute(sprintf('git diff --name-only %s %s 2> /dev/null', $this->argvInput[3], $this->argvInput[4]));


        $parsed = array();
        foreach ($gitDiffResult->output as $file) {
            // \GitHooks\Helper\ConsoleOutput::logger()->debug($file);
            $treeResult = $this->execute(sprintf('git ls-tree %s %s  2> /dev/null', trim($this->argvInput[5]), $file));

            if (count($treeResult->output) < 1) {
                // Found no existing file

                // Get all commits
                $commits = $this->execute(sprintf('git show --format=format:%%H --quiet %s..%s', $this->getOldRef(), $this->getNewRef()), false);
                for ($i = 0; $i < count($commits->output); $i++) {
                    $line = $commits->output[$i];
                    if (strpos($line, 'diff --git ') !== false) {
                        $commitShaIds[] = $commits->output[$i-1];
                    }
                }

                foreach ($commitShaIds as $index => $commit) {
                    $commitFiles = $this->execute(sprintf('git diff --name-only %s^..%s', $commit, $commit));
                    if ($index == 3) {
                        break;
                    }
                }



                /* $this->execute(sprintf('git diff --name-only %s^..%s', $this->getOldRef(), $this->getNewRef()));
                $this->execute(sprintf('git show --format=format:%H --quiet %s..%s', $this->getOldRef(), $this->getNewRef())); */


                /* $this->execute(sprintf('git rev-parse --verify %s 2> /dev/null', trim($this->argvInput[5])));
                $this->execute(sprintf('git diff-index --cached --full-index %s', $this->argvInput[3]));
                $this->execute(sprintf('git diff-index --full-index %s', $this->argvInput[3]));
                $this->execute(sprintf('git diff-index %s', $this->argvInput[3]));
                $this->execute(sprintf('git show %s:%s', $this->argvInput[4], $file));
                exec( "git diff-index --cached --full-index {$against}", $files );*/
            }


            // exec($commandLsTree, $tree, $return);

            /*
            if (count($tree) < 1) {
                $commandLsTree = sprintf('git ls-tree %s %s  2> /dev/null', '4b825dc642cb6eb9a060e54bf8d69288fbee4904', $file);
                echo $commandLsTree . PHP_EOL;
                exec($commandLsTree, $tree, $return);
            }
            */
            $tree = preg_split('/\s/', $treeResult->output[0]);

            $gitCatResult = $this->execute(sprintf('git cat-file %s %s 2> /dev/null', $tree[1], $tree[2]));
            if ($gitCatResult->return > 0) {
                echo "Could not run git cat-file\n\n";
                // exit(1);
            }

            $contents = implode("\n", $gitCatResult->output);

            $parsed[] = new GitFile($file, $contents);
        }

        return $parsed;
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
        return;
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