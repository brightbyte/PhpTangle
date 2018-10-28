<?php
/**
 * This file is part of PhpTangle.
 *
 * @license LGPL 2+
 * @author Daniel Kinzler
 */

namespace Wikimedia\PhpTangle\CLI;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Wikimedia\PhpTangle\Analysis\UsageExtractor;

/**
 * CLI command for analyzing files.
 */
class AnalyzeCommand extends Command {

	/**
	 * Analyze constructor.
	 */
	public function __construct() {
		parent::__construct();

		$this->setName( 'analyze' )
			->setDescription( 'Dependencies of the given directory' )
			->setHelp( 'This command analyzes the dependencies of the given directory.' )
			->addArgument( 'directory', InputArgument::REQUIRED, 'The directory to analyze.' );
	}

	public function execute( InputInterface $input, OutputInterface $output ) {
		$dir = $input->getArgument( 'directory' );

		$finder = new Finder();
		$finder->files()->name( '*.php' )->in( $dir )->sortByName();

		$extractor = new UsageExtractor();

		foreach ( $finder as $file ) {
			$fname = $file->getRelativePathname();

			$usages  = $extractor->extractFromFile( $file->getPathname() );

			$this->printUsages( $fname, $usages, $output );
		}
	}

	private function printUsages( string $fname, array $usages, OutputInterface $output ) {
		foreach ( $usages as $usage ) {
			$output->writeln( "$fname -> $usage" );
		}
	}

}
