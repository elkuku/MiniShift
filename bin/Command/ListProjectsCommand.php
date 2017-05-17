<?php

namespace MiniShift\Command;

use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Created by PhpStorm.
 * User: elkuku
 * Date: 17/05/17
 * Time: 9:18
 */
class ListProjectsCommand extends Command
{
	protected function configure()
	{
		$this
			->setName('projects')
			->setDescription('List projects.')
		;
	}

	public function execute(InputInterface $input, OutputInterface $output)
	{
		$fs = new Filesystem();
		$io = new SymfonyStyle($input, $output);

		try {
			//$fs->mkdir('/tmp/random/dir/'.mt_rand());
		} catch (IOExceptionInterface $e) {
			echo $e->getMessage();
		}

		$directories = glob($this->repoDir.'/*', GLOB_ONLYDIR);
		$projects = [];

		foreach ($directories as $directory)
		{
			$projects[] = substr($fs->makePathRelative($directory, $this->repoDir), 0, -5);
		}

		$table = new Table($output);
		$table->setHeaders(['Repo', 'Work', 'Web']);

		foreach ($projects as $project)
		{

			$table->addRow([
				$project.'.git',
				$fs->exists($this->workDir.'/'.$project) ? '<bg=green;fg=black> OK </>' : '<error> NO </error>',
				$fs->exists($this->webDir.'/'.$project) ? '<bg=green;fg=black> OK </>' : '<error> NO </error>',
			]);
		}

		$table->render();

		$io->text([
			'Root: '.ROOT,
			'Repo: '.$this->config->repoDir,
			'Work: '.$this->config->workDir,
			'Web : '.$this->config->webDir,
		]);
	}
}
