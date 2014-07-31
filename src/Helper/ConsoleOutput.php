<?php
/**
 * Created by PhpStorm.
 * User: sseidelmann
 * Date: 31.07.14
 * Time: 14:31
 */


namespace GitHooks\Helper;

class ConsoleOutput {

    private static $instance;

    private $logger;

    /**
     * Returns the logger.
     *
     * @return ConsoleOutput
     * @author Sebastian Seidelmann <sebastian.seidelmann@googlemail.com>
     */
    public static function logger() {
        if (!self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private function __construct() {
        $this->logger = new \Symfony\Component\Console\Output\ConsoleOutput();
    }

    /**
     * Writes a prefix.
     *
     * @author Sebastian Seidelmann <sebastian.seidelmann@googlemail.com>
     * @return void
     */
    private function writePrefix($color = 'blue') {
        $this->logger->write(sprintf('<fg=%s;options=bold>==></fg=%s;options=bold> ', $color, $color));
    }

    /**
     * Write a message to stdOut.
     *
     * @param string $message the message
     * @param bool   $newLine with newline?
     *
     * @author Sebastian Seidelmann <sebastian.seidelmann@googlemail.com>
     * @return void
     */
    public function write($message, $newLine = true) {
        $this->logger->write($message, $newLine);
    }

    /**
     * Debug a message to stdOut.
     *
     * @param string $message the message
     * @param bool   $newLine with newline?
     *
     * @author Sebastian Seidelmann <sebastian.seidelmann@googlemail.com>
     * @return void
     */
    public function debug($message, $newLine = true) {
        $this->writePrefix('blue');
        $this->write(sprintf(
            '<fg=white;options=bold>%s</fg=white;options=bold>',
            $message
        ), $newLine);
    }

    /**
     * Output a error message.
     *
     * @param string $message the message
     *
     * @author Sebastian Seidelmann <sebastian.seidelmann@googlemail.com>
     * @return void
     */
    public function error($message) {
        $this->writePrefix('red');
        $this->logger->write(sprintf(
            '<fg=white;options=bold>%s</fg=white;options=bold>',
            $message
        ), true);
    }

}