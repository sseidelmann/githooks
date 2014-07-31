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
            if ($file->isValidExtension('php')) {
                $result = exec(sprintf('echo %s | php -l 2>&1', escapeshellarg($file->getContent())), $output);
                if (strpos($result, 'Errors parsing') !== false) {
                    array_pop($output);
                    foreach ($output as $line) {
                        $this->addError($file, $line);
                    }
                }
            }
        }
    }

}