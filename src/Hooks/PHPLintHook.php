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
            $this->logger()->write('   isValidExtension: ' . $file->isValidExtension('php'));
/*
            $tmp = tempnam('/tmp/', 'phplint');
            file_put_contents($tmp, $file->getContent());
            echo "--------".PHP_EOL;
            @exec('php -l ' . $tmp, $return);
            echo "--------".PHP_EOL;
            foreach ($return as $line) {
                $this->addError($file, $line);
            }

            unlink($tmp);

*/
            $result = exec(sprintf('echo %s | php -l 2>&1', escapeshellarg($file->getContent())), $output);
            echo $result . PHP_EOL;
            print_r($output);


        }


        return true;
    }

}