<?php

namespace MiniShift\Command;

use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
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
class CreateProjectCommand extends Command
{
	protected function configure()
	{
		$this
			->setName('new')
			->setDescription('Create a new project.')
			->addArgument(
				'project',
				InputArgument::REQUIRED,
				'The project name'
			)
			->addArgument(
				'preserve',
				InputArgument::OPTIONAL,
				'Preserve permissions'
			)
		;
	}

	public function execute(InputInterface $input, OutputInterface $output)
	{
		$project = $input->getArgument('project');

		$fs = new Filesystem();
		$io = new SymfonyStyle($input, $output);

		$repoDirName = $project . '.git';

		if ($fs->exists($this->repoDir . '/' . $repoDirName))
		{
			throw new \UnexpectedValueException('Project exists.');
		}

		try {
			$io->title('Creating project '.$project);

			$io->section('Setting up repo dir');

			$fs->mkdir($this->repoDir . '/' . $repoDirName);
			echo shell_exec('cd "'.$this->repoDir . '/' . $repoDirName.'"; git --bare init 2>&1');

			$fs->copy(ROOT . '/tpl/hooks/pre-receive', $this->repoDir . '/' . $repoDirName . '/hooks/pre-receive');
			$fs->copy(ROOT . '/tpl/hooks/post-receive', $this->repoDir . '/' . $repoDirName . '/hooks/post-receive');

			$io->section('Creating working copy');

			echo shell_exec('cd "'.$this->workDir . '"; git clone '.$this->repoDir . '/' . $repoDirName.' 2>&1');

			if (!$input->getArgument('preserve'))
			{
				$fs->chown($this->repoDir . '/' . $repoDirName, $this->config->gitUser, true);
				$fs->chgrp($this->repoDir . '/' . $repoDirName, $this->config->gitUser, true);
				$fs->chown($this->workDir . '/' . $project, $this->config->gitUser, true);
				$fs->chgrp($this->workDir . '/' . $project, $this->config->gitUser, true);
			}

			$io->section('Creating symlink in web dir');
			$fs->symlink($this->workDir . '/' . $project, $this->webDir . '/' . $project);

			$host = exec('hostname');
			$ip = exec('hostname -I');

			$table = new Table($output);
			$table
				->setHeaders(['', 'Host', 'Ip'])
				->setRows([
					[
						'<info>Access</info>', "http://$host/$project", "http://$ip/$project"],
					[
						'<info>Clone</info>',
						"{$this->config->gitUser}@$host:{$this->repoDir}/$project.git",
						"{$this->config->gitUser}@$ip:{$this->repoDir}/$project.git",
					],
				])
			;

			$io->success('Project has been created.');
			$table->render();


		} catch (IOExceptionInterface $e) {
			echo $e->getMessage();
		}
	}
}
