<?php
/**
 * Created by PhpStorm.
 * User: sseidelmann
 * Date: 31.07.14
 * Time: 13:05
 */

namespace GitHooks\Hooks;

use GitHooks\AbstractHook;

class PHPCSHook extends AbstractHook {

    /**
     * Defines the default standard.
     *
     * @author Sebastian Seidelmann <sebastian.seidelmann@twt.de>
     * @var    string
     */
    const DEFAULT_STANDARD = 'PSR2';

    /**
     * Starts the hook.
     *
     * @return mixed
     * @author Sebastian Seidelmann <sebastian.seidelmann@twt.de>
     */
    public function run() {


        foreach ($this->getFiles() as $file) {
            if ($file->isValidExtension('php')) {

                $tempname = tempnam('/tmp/', 'phpcs') . '.php';
                file_put_contents($tempname, $file->getContent());

                $command = sprintf(
                    '%s --standard=%s --report=xml %s',
                    $this->getPHPCSExecutablePath(),
                    $this->getStandard(),
                    $tempname
                );

                $output = array();
                exec($command, $output);


                if (count($output) > 0) {
                    $xml = new \SimpleXMLElement(implode("\n", $output));

                    /* @var $error SimpleXMLElement */
                    if (count($xml->file->error) > 0) {
                        foreach ($xml->file->error as $error) {
                            // print_r($error);
                            $attributes = $error->attributes();
                            $line       = $attributes['line'];
                            $column     = $attributes['column'];
                            $message    = (string) $error;

                            $this->addError($file, sprintf('%s on line %s', $message, $line));
                        }
                    }
                }
                unlink($tempname);
            }
        }


        return false;
    }

    /**
     * Returns the path to executable.
     *
     * @return string
     * @author Sebastian Seidelmann <sebastian.seidelmann@twt.de>
     */
    private function getPHPCSExecutablePath() {
        return VENDOR_DIRECTORY . 'bin' . DIRECTORY_SEPARATOR . 'phpcs';
    }


    /**
     * Returns the CS Standard.
     *
     * @return string
     * @author Sebastian Seidelmann <sebastian.seidelmann@twt.de>
     */
    private function getStandard() {

        $standard = $this->getConfig('standard');
        if (!$standard) {
            $standard == self::DEFAULT_STANDARD;
        }

        exec(sprintf('%s -i', $this->getPHPCSExecutablePath()), $output);
        $standardsInstalled = explode(' ', str_replace(array('The installed coding standards are ', ','), '', $output[0]));
        if (!in_array($standard, $standardsInstalled)) {
            $standard = $standardsInstalled[0];
        }

        return $standard;
    }

}