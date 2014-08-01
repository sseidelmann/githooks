<?php
/**
 * Created by PhpStorm.
 * User: sseidelmann
 * Date: 31.07.14
 * Time: 13:38
 */

namespace GitHooks;


use GitHooks\Helper\ConsoleOutput;
use GitHooks\Helper\GitFile;

abstract class AbstractHook {

    const DEFAULT_PRIORITY = 1000;

    private $config;

    /**
     * @var GitFile[]
     */
    private $files;

    /**
     * Saves the errors.
     *
     * @var    array
     * @author Sebastian Seidelmann <sebastian.seidelmann@googlemail.com>
     */
    private static $errors = array();

    /**
     * Creates the hook instance.
     *
     * @param $config
     *
     * @author Sebastian Seidelmann <sebastian.seidelmann@googlemail.com>
     * @return void
     */
    public final function __construct($config) {
        $this->config = $config;
    }

    /**
     * Adds the files to check.
     *
     * @param array $files the files
     *
     * @author Sebastian Seidelmann <sebastian.seidelmann@googlemail.com>
     * @return void
     */
    public final function setFiles(array $files = array()) {
        $this->files = $files;
    }

    /**
     * Returns the files for this push.
     *
     * @return Helper\GitFile[]
     * @author Sebastian Seidelmann <sebastian.seidelmann@googlemail.com>
     */
    protected function getFiles() {
        return $this->files;
    }

    /**
     * Returns the priority
     *
     * @author Sebastian Seidelmann <sebastian.seidelmann@googlemail.com>
     * @return int
     */
    public function getPriority() {
        return isset($this->config['priority'])?$this->config['priority']:self::DEFAULT_PRIORITY;
    }

    /**
     * Returns the name of the hook.
     *
     * @author Sebastian Seidelmann <sebastian.seidelmann@googlemail.com>
     * @return string
     */
    public function getName($simplyfied = false) {
        if ($simplyfied) {
            return strtolower(end(explode('\\', $this->getName())));
        }
        return get_called_class();
    }

    /**
     * Starts the hook.
     *
     * @return bool
     * @author Sebastian Seidelmann <sebastian.seidelmann@googlemail.com>
     */
    abstract public function run();

    /**
     * Returns a logger instance.
     *
     * @return ConsoleOutput
     * @author Sebastian Seidelmann <sebastian.seidelmann@googlemail.com>
     */
    protected function logger() {
        return ConsoleOutput::logger();
    }


    /**
     * Adds an error to the output.
     *
     * @param string $file  the current file.
     * @param string $error the error message
     *
     * @author Sebastian Seidelmann <sebastian.seidelmann@googlemail.com>
     * @return void
     */
    protected function addError(GitFile $file, $error) {
        //$this->errors[$file->getName()][$this->getName(true)][] = array(
        self::$errors[$file->getName()][str_replace('hook', '', $this->getName(true))][] = array(
            'file'  => $file,
            'error' => $error,
            'hook'  => get_called_class()
        );
    }

    /**
     * Returns the erros for the given file.
     *
     * @param GitFile $file the file
     *
     * @return array
     * @author Sebastian Seidelmann <sebastian.seidelmann@twt.de>
     */
    protected function getErrorsForFile(GitFile $file) {
        if (isset(self::$errors[$file->getName()])) {
            return self::$errors[$file->getName()];
        }
        return array();
    }

    /**
     * Returns the error array.
     *
     * @return array
     * @author Sebastian Seidelmann <sebastian.seidelmann@googlemail.com>
     */
    public function getErrors() {
        //return $this->errors;
        return self::$errors;
    }

    /**
     * Returns the config key.
     *
     * @param string $key the parameter
     *
     * @return mixed
     * @author Sebastian Seidelmann <sebastian.seidelmann@googlemail.com>
     */
    protected function getConfig($key) {
        return isset($this->config[$key])?$this->config[$key]:false;
    }
}