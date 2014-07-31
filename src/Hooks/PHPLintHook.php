<?php
/**
 * Created by PhpStorm.
 * User: sseidelmann
 * Date: 31.07.14
 * Time: 13:05
 */

namespace GitHooks\Hooks;

use GitHooks\AbstractHook;

class PHPLintHook extends AbstractHook {

    /**
     * Starts the hook.
     *
     * @return mixed
     * @author Sebastian Seidelmann <sebastian.seidelmann@twt.de>
     */
    public function run() {
        // echo __METHOD__ . PHP_EOL;


        return true;
    }

}