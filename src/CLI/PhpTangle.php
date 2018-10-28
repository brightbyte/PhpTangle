<?php
/**
 * This file is part of PhpTangle.
 *
 * @license LGPL 2+
 * @author Daniel Kinzler
 */

namespace Wikimedia\PhpTangle\CLI;

use Symfony\Component\Console\Application;

/**
 * Responsible for running phptangle.
 */
class PhpTangle extends Application {
	public function __construct() {
		parent::__construct( 'PhpTangle' );

		$this->add( new AnalyzeCommand() );

		$this->setDefaultCommand( 'analyze', true );
	}
}
