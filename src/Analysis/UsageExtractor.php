<?php
/**
 * This file is part of PhpTangle.
 *
 * @license LGPL 2+
 * @author Daniel Kinzler
 */

namespace Wikimedia\PhpTangle\Analysis;

use PHP_Token;
use PHP_Token_AS;
use PHP_Token_CLASS;
use PHP_Token_COMMENT;
use PHP_Token_CURLY_OPEN;
use PHP_Token_DOLLAR_OPEN_CURLY_BRACES;
use PHP_Token_EXTENDS;
use PHP_Token_IMPLEMENTS;
use PHP_Token_INTERFACE;
use PHP_Token_NAMESPACE;
use PHP_Token_OPEN_CURLY;
use PHP_Token_SEMICOLON;
use PHP_Token_Stream;
use PHP_Token_TRAIT;
use PHP_Token_USE;
use Wikimedia\PhpTangle\Model\Reference;
use Wikimedia\PhpTangle\Model\Resource;
use Wikimedia\PhpTangle\Model\ResourceTypes;

/**
 * Extracts usages information from PHP files.
 */
class UsageExtractor {

	/**
	 * @var PHP_Token_Stream
	 */
	private $stream;

	/**
	 * @var string
	 */
	private $fileName;

	/**
	 * @param string $fileName The PHP file to extract usages from
	 */
	public static function newFromFileName( $fileName ) {
		return new static( new PHP_Token_Stream( $fileName ), $fileName );
	}

	/**
	 * @param PHP_Token_Stream $stream
	 * @param string $fileName
	 */
	public function __construct( PHP_Token_Stream $stream, $fileName = '' ) {
		$this->stream = $stream;
		$this->fileName = $fileName;
	}

	/**
	 * @return Resource
	 */
	public function extract(): Resource {
		$namespace = '';
		$rcName = $this->fileName;
		$rcType = ResourceTypes::FILE_RESOURCE;
		$usages = [];

		$scanFor = [
			PHP_Token_USE::class, PHP_Token_NAMESPACE::class, PHP_Token_CLASS::class,
			PHP_Token_INTERFACE::class, PHP_Token_TRAIT::class,
		];

		while ( $token = $this->scanTo( ...$scanFor ) ) {
			$type = get_class( $token );

			switch ( $type ) {
				case PHP_Token_USE::class:
					// TODO: trim leading backslash!
					$target = $this->readTo( PHP_Token_SEMICOLON::class );
					$usages[] = new Reference( Reference::DECLARED_VISIBILITY,
						Reference::USE_MODE,
						ResourceTypes::TYPE_RESOURCE,
						$target );
					break;
				case PHP_Token_NAMESPACE::class:
					$namespace = $this->readTo( PHP_Token_CURLY_OPEN::class,
						PHP_Token_OPEN_CURLY::class,
						PHP_Token_DOLLAR_OPEN_CURLY_BRACES::class,
						PHP_Token_SEMICOLON::class,
						PHP_Token_AS::class );
					break;
				case PHP_Token_CLASS::class:
					$rcType = ResourceTypes::CLASS_RESOURCE;
					$rcName = $this->readTo( PHP_Token_CURLY_OPEN::class,
						PHP_Token_OPEN_CURLY::class,
						PHP_Token_DOLLAR_OPEN_CURLY_BRACES::class,
						PHP_Token_EXTENDS::class,
						PHP_Token_IMPLEMENTS::class );
					break;
				case PHP_Token_INTERFACE::class:
					$rcType = ResourceTypes::INTERFACE_RESOURCE;
					$rcName = $this->readTo( PHP_Token_CURLY_OPEN::class,
						PHP_Token_OPEN_CURLY::class,
						PHP_Token_DOLLAR_OPEN_CURLY_BRACES::class,
						PHP_Token_EXTENDS::class );
					break;
				case PHP_Token_TRAIT::class:
					$rcType = ResourceTypes::TRAIT_RESOURCE;
					$rcName = $this->readTo( PHP_Token_CURLY_OPEN::class,
						PHP_Token_OPEN_CURLY::class,
						PHP_Token_DOLLAR_OPEN_CURLY_BRACES::class,
						PHP_Token_EXTENDS::class );
					break;
			}
		}

		$rcName = $namespace ? "$namespace\\$rcName" : $rcName;
		$resource = new Resource( $rcType, $rcName, $usages );

		return $resource;
	}

	/**
	 * @param mixed ...$ttypes
	 *
	 * @return PHP_Token|null
	 */
	private function scanTo( ...$ttypes ) {
		while ( $token = $this->nextToken() ) {
			$type = get_class( $token );

			if ( in_array( $type, $ttypes ) ) {
				return $token;
			}
		}

		return null;
	}

	private function readTo( ...$ttypes ): string {
		$text = [];

		while ( $token = $this->nextToken() ) {
			$type = get_class( $token );

			if ( $type === PHP_Token_COMMENT::class ) {
				continue;
			}

			if ( in_array( $type, $ttypes ) ) {
				break;
			}

			$text[] = "$token";
		}

		return trim( implode( '', $text ) );
	}

	/**
	 * @return PHP_Token|null
	 */
	private function nextToken() {
		if ( !$this->stream->valid() ) {
			return null;
		}

		$token = $this->stream->current();
		$this->stream->next();

		return $token;
	}

}
