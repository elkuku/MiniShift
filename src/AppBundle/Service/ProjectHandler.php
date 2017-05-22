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
class ProjectHandler
{
    private $repoDir = '';
    private $workDir = '';
    private $webDir = '';
    private $gitUser = '';

    /**
     * @var Filesystem
     */
    private $fs;

    /**
     * ProjectLister constructor.
     *
     * @param string $rootDir
     * @param string $gitUser
     */
    public function __construct(string $rootDir, string $gitUser)
    {
        $root = realpath($rootDir.'/..');

        $this->repoDir = $root.'/repo';
        $this->workDir = $root.'/work';
        $this->webDir  = $root.'/web';
        $this->gitUser = $gitUser;

        $this->fs = new FileSystem();
    }

    /**
     * Get the projects list.
     *
     * @return array
     */
    public function getProjects(): array
    {
        $host = exec('hostname');
        $ip   = exec('hostname -I');

        $projects = [];

        foreach (new \DirectoryIterator($this->repoDir) as $iterator) {
            if ($iterator->isDot() || false == $iterator->isDir()) {
                continue;
            }

            $directory = $iterator->getBasename();

            if (0 === strpos($directory, '.')) {
                continue;
            }

            $project = new \stdClass();

            $project->dir        = substr($iterator->getBasename(), 0, -4);
            $project->gitDir     = $this->getRepoDirName($project->dir);
            $project->hasWorkDir = $this->fs->exists($this->workDir.'/'.$project->dir);
            $project->hasWebDir  = $this->fs->exists($this->webDir.'/'.$project->dir);
            $project->cloneHost  = "$this->gitUser@$host:$this->repoDir/$project->gitDir";
            $project->cloneIp    = "$this->gitUser@$ip:$this->repoDir/$project->gitDir";

            $projects[$project->dir] = $project;
        }

        ksort($projects);

        return $projects;
    }

    /**
     * @param string $project
     *
     * @return $this
     */
    public function rm(string $project): ProjectHandler
    {
        if (false === $this->hasRepo($project)) {
            throw new \UnexpectedValueException('Invalid project.');
        }

        $this->fs->remove($this->repoDir.'/'.$this->getRepoDirName($project));
        $this->fs->remove($this->workDir.'/'.$project);
        $this->fs->remove($this->webDir.'/'.$project);

        return $this;
    }

    /**
     * @param string $project
     *
     * @return bool
     */
    private function hasRepo(string $project): bool
    {
        return $this->fs->exists($this->repoDir.'/'.$this->getRepoDirName($project));
    }

    /**
     * @param string $project
     *
     * @return string
     */
    private function getRepoDirName(string $project): string
    {
        return $project.'.git';
    }
}
