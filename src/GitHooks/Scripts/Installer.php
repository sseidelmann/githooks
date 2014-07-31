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

    public static function postPackageInstall(Event $event) {
        $event->getIO()->write('Executing the event "postPackageInstall"');
        echo 'Executing the event "postPackageInstall"' . PHP_EOL;
    }

    public static function postUpdateCmd(Event $event) {
        $event->getIO()->write('Executing the event "postUpdateCmd"');
        echo 'Executing the event "postUpdateCmd"' . PHP_EOL;
    }

}