<?php
/**
 * Created by PhpStorm.
 * User: elkuku
 * Date: 25/05/17
 * Time: 11:19
 */

namespace AppBundle\Entity;

/**
 * Class Project
 * @package AppBundle\Entity
 */
class Project
{
    public $dir = '';
    public $gitDir = '';
    public $hasWorkDir = '';
    public $hasWebDir = '';
    public $cloneHost = '';
    public $cloneIp = '';
}
