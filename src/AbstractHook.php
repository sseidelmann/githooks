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
     * @author Sebastian Seidelmann <sebastian.seidelmann@twt.de>
     * @return string
     */
    public function getName() {
        return get_called_class();
    }

    /**
     * Starts the hook.
     *
     * @return bool
     * @author Sebastian Seidelmann <sebastian.seidelmann@twt.de>
     */
    abstract public function run();

    /**
     * Returns a logger instance.
     *
     * @return ConsoleOutput
     * @author Sebastian Seidelmann <sebastian.seidelmann@twt.de>
     */
    protected function logger() {
        return ConsoleOutput::logger();
    }
}