<?php
/**
 * This file is part of PhpTangle.
 *
 * @license LGPL 2+
 * @author Daniel Kinzler
 */

namespace Wikimedia\PhpTangle\Analysis;

use PHP_Token_COMMENT;
use PHP_Token_SEMICOLON;
use PHP_Token_Stream;
use PHP_Token_USE;
use PHP_Token_WHITESPACE;
use Wikimedia\PhpTangle\Model\Resource;

/**
 * Extracts usages information from PHP files.
 */
class UsageExtractor {

	/**
	 * @param string $file The PHP file to extract usages from
	 *
	 * @return Resource
	 */
	public function extractFromFile( $file ): Resource {
		return $this->extractFromStream( new PHP_Token_Stream( $file ) );
	}

	/**
	 * @param PHP_Token_Stream $stream A token stream to extract usages from
	 *
	 * @return Resource
	 */
	public function extractFromStream( PHP_Token_Stream $stream ): Resource {
		$state = '';
		$usages = [];
		$current = '';

		foreach ( $stream as $token ) {
			$type = get_class( $token );

			switch ( $state ) {
				case PHP_Token_USE::class:
					switch ( $type ) {
						case PHP_Token_WHITESPACE::class:
						case PHP_Token_COMMENT::class:
							continue 3; // keep $state unchanged

						case PHP_Token_SEMICOLON::class:
							$usages[] = $current;
							$current = '';
							break 2; // go to end of loop and update $state

						default:
							$current .= "$token";
							continue 3; // keep $state unchanged
					}
			}

			$state = $type;
		}

		$resource = new Resource( '???', '???', $usages );
		return $resource;
	}

}
