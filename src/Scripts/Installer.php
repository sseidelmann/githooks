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
        $io = $event->getIO();
        $io->write('Executing the event "postUpdateCmd"');

        $pwd = realpath(getcwd()) . DIRECTORY_SEPARATOR;


        self::createBinary($pwd);


    }

    private static function createBinary($pwd) {
        exec('cd '.$pwd.' && ln -s vendor/sseidelmann/githooks/bin/hook hook');
    }

}