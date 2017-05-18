<?php

namespace MiniShift\Console;

use Symfony\Component\Console\Command\Command as ConsoleCommand;

class Command extends ConsoleCommand
{
    protected $config;

    protected $repoDir;
    protected $workDir;
    protected $webDir;

    public function __construct($name = null)
    {
        $this->config = json_decode(file_get_contents(ROOT.'/config.json'));

        if (!$this->config) {
            throw new \UnexpectedValueException('Invalid config file.');
        }

        $this->repoDir = ROOT.'/'.$this->config->repoDir;
        $this->workDir = ROOT.'/'.$this->config->workDir;
        $this->webDir  = ROOT.'/'.$this->config->webDir;

        parent::__construct($name);
    }
}
