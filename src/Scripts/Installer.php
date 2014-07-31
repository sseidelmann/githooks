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

    public static function postUpdateCmd(Event $event) {
        /* @var $io \Composer\IO\ConsoleIO */
        $io = $event->getIO();

        $io->write('');

        $pwd = realpath(getcwd()) . DIRECTORY_SEPARATOR;


        $io->write('<fg:cyan>-> Creating new binary file.</fg:cyan>');
        self::createBinary($pwd);
        self::createConfig($pwd);




        $io->write('<info>Usage:</info>');
    }

    private static function createBinary($pwd) {
        if (!file_exists($pwd . 'hook')) {
            exec('cd '.$pwd.' && ln -s vendor/sseidelmann/githooks/bin/hook hook');
        }
    }


    private static function createConfig($pwd) {
        $file = $pwd . 'config.json';

        if (!file_exists($file)) {
            file_put_contents('config.json', '{
                "hooks": []
            }');
        }
    }
}