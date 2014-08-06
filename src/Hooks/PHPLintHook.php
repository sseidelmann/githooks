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
     * Saves the checked files.
     *
     * @author Sebastian Seidelmann <sebastian.seidelmann@twt.de>
     * @var    array
     */
    private static $checked = array();

    /**
     * Starts the hook.
     *
     * @return mixed
     * @author Sebastian Seidelmann <sebastian.seidelmann@googlemail.com>
     */
    public function run() {

        foreach ($this->getFiles() as $file) {
            if ($file->isValidExtension('php') && !in_array($file, self::$checked)) {


                $tempname = tempnam('/tmp/', 'phpcs') . '.php';
                $contents = $file->getContent();
                file_put_contents($tempname, $contents);

                $output = array();
                // $result = exec(sprintf('echo %s | php -l 2>&1', escapeshellarg($file->getContent())), $output);
                $result = exec(sprintf('php -l %s 2>&1', $tempname), $output);
                if (strpos($result, 'Errors parsing') !== false) {
                    array_pop($output);
                    foreach ($output as $line) {
                        $this->addError($file, $line);
                    }
                }

                unset($tempname);

                self::$checked[] = $file;
            }
        }
    }

}