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
     * Saves the name of the file.
     *
     * @var    string
     * @author Sebastian Seidelmann <sebastian.seidelmann@googlemail.com>
     */
    private $name;

    /**
     * Saves the content of the file.
     *
     * @var    string
     * @author Sebastian Seidelmann <sebastian.seidelmann@googlemail.com>
     */
    private $content;

    /**
     * Saves the commit sha of the file.
     *
     * @var    string
     * @author Sebastian Seidelmann <sebastian.seidelmann@googlemail.com>
     */
    private $commit;

    public function __construct($name, $content, $commit) {
        $this->name    = $name;
        $this->content = $content;
        $this->commit  = $commit;
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
     * Returns the content of the file.
     *
     * @author Sebastian Seidelmann <sebastian.seidelmann@googlemail.com>
     * @return string
     */
    public function getContent() {
        return $this->content;
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
}