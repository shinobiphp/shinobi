<?php
declare(strict_types=1);

namespace Shinobi\Commands\Pkg;

use Symfony\Console\Command\Command

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

#[AsCommand(name: 'pkg:install')]
class Install extends Command
{
	protected function configure(): void
	{
		$this->addArgument('pkgs', 
		        InputArgument::IS_ARRAY | InputArgument::REQUIRED,
			'names of packages you want to install (seperated with a space)');

	}


	public function initialize(InputInterface $input, OutputInterface $output): void
	{
        	// 
	}

	public function interact(InputInterface $input, OutputInterface $output): void
	{
        	// ...
	}

	public function __invoke(InputInterface $input, OutputInterface $output): int
	{
		$pkg_names = $input->getArgument('pkgs');
		if (count($pkg_names) <= 0) {
			$output->writeln('<error>No packages specified</>');

			return Command::INVALID;
		}
		
		$which_path =  trim(shell_exec('which which'));

		if (empty($which_path)) {
			$output->writeln('<error>Could not find which</i>');

			return Command::FAILURE;
		}

		$composer_path = trim(shell_exec($which_path . ' composer'));

		if (empty($composer_path)) {
			$output->writeln('<error>Could not find composer</i>');
		}

		$composer_cmd = "%s require shinobiphp/%s";

		foreach ($pkg_names as $idx => $pn) {
			$cmd = sprintf($composer_cmd, $composer_path, $pn);
			$output->writeln('<fg=white>Executing \'<fg=cyan>' . $cmd . '</>\'</>');
			
			exec($cmd);
			$output->writeln('<fg=yellow>Done!</>');
		}

		$output->writeln('<fg=white>Executing \'<fg=cyan>' . $composer_path . ' install</>\'</>');
		exec($composer_path . ' install');

		return Command::SUCCESS;	
	}
}
