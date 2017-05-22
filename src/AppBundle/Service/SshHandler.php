<?php
/**
 * Created by PhpStorm.
 * User: elkuku
 * Date: 16/05/17
 * Time: 2:45
 */

namespace AppBundle\Service;

use Pagemachine\AuthorizedKeys\AuthorizedKeys;

/**
 * Class SshHandler
 * @package AppBundle\Service
 */
class SshHandler extends AuthorizedKeys
{
    private $path = '';

    /**
     * SshHandler constructor.
     *
     * @param string $gitUser
     */
    public function __construct(string $gitUser)
    {
        $this->path = "/home/$gitUser/.ssh/authorized_keys";

        $content = (file_exists($this->path)) ? file_get_contents($this->path) : null;

        parent::__construct($content);
    }

    /**
     * Save to disk.
     */
    public function save()
    {
        $this->toFile($this->path);
    }
}
