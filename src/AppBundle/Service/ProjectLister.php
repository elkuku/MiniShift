<?php
/**
 * Created by PhpStorm.
 * User: elkuku
 * Date: 16/05/17
 * Time: 2:45
 */

namespace AppBundle\Service;

use Symfony\Component\Filesystem\Filesystem;

/**
 * Class ProjectLister
 * @package AppBundle\Service
 */
class ProjectLister
{
    private $repoDir = '';
    private $workDir = '';
    private $webDir = '';
    private $gitUser = '';

    /**
     * ProjectLister constructor.
     *
     * @param string $rootDir
     * @param string $repoDir
     * @param string $workDir
     * @param string $webDir
     * @param string $gitUser
     */
    public function __construct($rootDir, $repoDir, $workDir, $webDir, $gitUser)
    {
        $root = realpath($rootDir.'/..');

        $this->repoDir = $root.'/'.$repoDir;
        $this->workDir = $root.'/'.$workDir;
        $this->webDir = $root.'/'.$webDir;
        $this->gitUser = $gitUser;
    }

    /**
     * Get the projects list.
     *
     * @return array
     */
    public function getProjects(): array
    {
        $fs = new Filesystem();

        $host = exec('hostname');
        $ip   = exec('hostname -I');

        $directories = glob($this->repoDir.'/*', GLOB_ONLYDIR);
        $projects    = [];

        foreach ($directories as $directory) {
            $project = new \stdClass();

            $project->dir = substr($fs->makePathRelative($directory, $this->repoDir), 0, -5);
            $project->gitDir = $project->dir.'.git';
            $project->hasWorkDir = $fs->exists($this->workDir.'/'.$project->dir);
            $project->hasWebDir = $fs->exists($this->webDir.'/'.$project->dir);
            $project->cloneHost = "$this->gitUser@$host:$this->repoDir/$project->gitDir";
            $project->cloneIp = "$this->gitUser@$ip:$this->repoDir/$project->gitDir";

            $projects[] = $project;
        }

        return $projects;
    }
}
