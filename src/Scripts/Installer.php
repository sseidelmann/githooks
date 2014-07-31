<?php
/**
 * Created by PhpStorm.
 * User: sseidelmann
 * Date: 31.07.14
 * Time: 10:50
 */

namespace GitHooks\Scripts;

use Composer\Script\Event;


class Installer {


    private $io;

    public function __construct(Event $event) {
        $this->io = $event->getIO();
    }

    public function write($message) {
        $this->io->write(sprintf('<fg=blue;options=bold>==></fg=blue;options=bold> <fg=white;options=bold>%s</fg=white;options=bold>', $message));
    }



    public static function postUpdateCmd(Event $event) {

        $instance = new self($event);

        /* @var $io \Composer\IO\ConsoleIO */
        $io = $event->getIO();

        $io->write('');

        $pwd = realpath(getcwd()) . DIRECTORY_SEPARATOR;


        $instance->createBinary($pwd);
        $instance->createConfig($pwd);



        $instance->write('Usage:');
        $io->write('   ./hook check pre-receive');
    }



    public function createBinary($pwd) {
        $this->write('Updating binary file');
        if (!file_exists($pwd . 'hook')) {
            exec('cd '.$pwd.' && ln -s vendor/sseidelmann/githooks/bin/hook hook');
        }
    }


    public function createConfig($pwd) {
        $file = $pwd . 'config.json';

        if (!file_exists($file)) {
            $this->write('Creating new config file');
            file_put_contents('config.json', '{
                "hooks": []
            }');
        }
    }
}