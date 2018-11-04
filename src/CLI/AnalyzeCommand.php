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
use Wikimedia\PhpTangle\Model\Resource;

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

		foreach ( $finder as $file ) {
			$extractor = UsageExtractor::newFromFileName( $file->getPathname() );
			$resource = $extractor->extract();

			$this->printResource( $resource, $output );
		}
	}

	private function printResource( Resource $resource, OutputInterface $output ) {
		$usages = $resource->getUsages();

		$output->writeln( "$resource:" );
		foreach ( $usages as $usage ) {
			$output->writeln( "\t$usage" );
		}
	}

}
