<?php
/**
 * Created by PhpStorm.
 * User: elkuku
 * Date: 16/05/17
 * Time: 2:45
 */

namespace AppBundle\Service;

/**
 * Class ShaFinder
 * @package AppBundle\Service
 */
class ShaFinder
{
    private $sha = 'n/a';

    /**
     * ShaFinder constructor.
     *
     * @param string $rootDir
     */
    public function __construct($rootDir)
    {
        $dir = realpath($rootDir.'/..');

        if (file_exists($dir.'/sha.txt')) {
            $this->sha = file_get_contents($dir.'/sha.txt');
        } elseif (file_exists($dir.'/.git/refs/heads/master')) {
            $this->sha = file_get_contents($dir.'/.git/refs/heads/master');
        }
    }

    /**
     * Get the current SHA.
     *
     * @return string
     */
    public function getSha()
    {
        return $this->sha;
    }
}
