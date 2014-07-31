<?php
/**
 * Created by PhpStorm.
 * User: sseidelmann
 * Date: 31.07.14
 * Time: 13:05
 */

namespace GitHooks\Hooks;

use GitHooks\AbstractHook;
use GitHooks\Helper\ConsoleOutput;

class PHPLintHook extends AbstractHook {

    /**
     * Starts the hook.
     *
     * @return mixed
     * @author Sebastian Seidelmann <sebastian.seidelmann@twt.de>
     */
    public function run() {

        foreach ($this->getFiles() as $file) {

            $this->logger()->debug('Checking ' . $file->getName());
            $this->logger()->debug('  isDeleted:        ' . $file->isDeleted());
            $this->logger()->debug('  isValidExtension: ' . $file->isValidExtension('php'));

        }


        return true;
    }

}