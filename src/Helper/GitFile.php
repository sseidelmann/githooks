<?php
/**
 * Created by PhpStorm.
 * User: sseidelmann
 * Date: 31.07.14
 * Time: 15:34
 */


namespace GitHooks\Helper;

class GitFile {

    /**
     * Defines the status of the file for Deletion.
     *
     * @var    string
     * @author Sebastian Seidelmann <sebastian.seidelmann@googlemail.com>
     */
    const FILE_STATUS_DELETED = 'D';

    private $raw;

    /**
     * Saves the name of the file.
     *
     * @var    string
     * @author Sebastian Seidelmann <sebastian.seidelmann@googlemail.com>
     */
    private $name;

    /**
     * Saves the status of the file.
     *
     * @var    string
     * @author Sebastian Seidelmann <sebastian.seidelmann@googlemail.com>
     */
    private $status;

    public function __construct($indexDiff) {
        $this->raw = $indexDiff;
        $this->parseRawIndexDiff();
    }

    /**
     *
     *
    [0] => :100644 000000 409b978e24e4880c5a546f471c5a2ab364c8a8f3 0000000000000000000000000000000000000000 D	.gitignore
    [1] => :100644 000000 c0e0a8cfcb0900e1c81abbd69f8947909ac0c9ed 0000000000000000000000000000000000000000 D	README.md
    [2] => :100644 000000 e8dc13eefb4c5e198df6838ac2a17a3046d04e4e 0000000000000000000000000000000000000000 D	dynamicReturnTypeMeta.json
     */
    private function parseRawIndexDiff() {
        $parts  = explode(" ", $this->raw);
        $sha    = $parts[3];
        $this->name   = substr($parts[4], 2);
        $this->status = substr($parts[4], 0, 1);
    }

    /**
     * Returns the name of the file.
     *
     * @author Sebastian Seidelmann <sebastian.seidelmann@googlemail.com>
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Returns boolean if extension matches.
     *
     * @param string $extension the extension.
     *
     * @author Sebastian Seidelmann <sebastian.seidelmann@googlemail.com>
     * @return bool
     */
    public function isValidExtension($extension) {
        $pattern = sprintf('/\.%s$/', $extension);
        return preg_match($pattern, $this->getName());
    }

    /**
     * Checks if file is deleted.
     *
     * @return bool
     * @author Sebastian Seidelmann <sebastian.seidelmann@googlemail.com>
     */
    public function isDeleted() {
        return $this->status == self::FILE_STATUS_DELETED;
    }
}