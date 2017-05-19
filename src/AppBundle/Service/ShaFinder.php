<?php
/**
 * Created by PhpStorm.
 * User: elkuku
 * Date: 16/05/17
 * Time: 2:45
 */

namespace AppBundle\Service;

class ShaFinder
{
	private $sha = 'n/a';

	public function __construct($rootDir)
	{
		$dir = realpath($rootDir.'/..');

		if (file_exists($dir . '/sha.txt'))
		{
			$this->sha = file_get_contents($dir . '/sha.txt');
		}
	}

	public function getSha()
	{
		return $this->sha;
	}
}
