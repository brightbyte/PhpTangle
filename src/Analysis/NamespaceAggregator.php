<?php
/**
 * This file is part of PhpTangle.
 *
 * @license LGPL 2+
 * @author Daniel Kinzler
 */

namespace Wikimedia\PhpTangle\Analysis;

use InvalidArgumentException;
use Wikimedia\PhpTangle\Model\Reference;
use Wikimedia\PhpTangle\Model\Resource;
use Wikimedia\PhpTangle\Model\ResourceTypes;

/**
 * Aggregates Resource objects describing namespaces from Resource objects describing classes.
 */
class NamespaceAggregator {

	/**
	 * @var Resource[]
	 */
	private $namespaces = [];

	public function addResource( Resource $rc ) {
		switch ( $rc->getType() ) {
			case ResourceTypes::TYPE_RESOURCE:
			case ResourceTypes::CLASS_RESOURCE:
			case ResourceTypes::INTERFACE_RESOURCE:
			case ResourceTypes::TRAIT_RESOURCE:
				$this->addTypeResource( $rc );
				break;
			default:
				throw new InvalidArgumentException( "Unsopported resoucre type: " . $rc->getType() );
		}
	}

	/**
	 * @return Resource[]
	 */
	public function getNamespaces(): array {
		return $this->namespaces;
	}

	/**
	 * @param Resource $rc
	 */
	private function addTypeResource( Resource $rc ) {
		$namespaceRefs = [];

		foreach ( $rc->getUsages() as $usage ) {
			// TODO: support all modes!
			if ( $usage->getMode() === Reference::USE_MODE ) {
				$namespaceName = $this->getNamespaceFromTypeName( $usage->getResourceName() );

				if ( !isset( $namespaceRefs[ $namespaceName ] ) ) {
					$namespaceRefs[ $namespaceName ] = new Reference(
						$usage->getVisibility(),
						$usage->getMode(),
						ResourceTypes::NAMESPACE_RESOURCE,
						$namespaceName
					);
				}
			}
		}

		$rcNamespace = $this->getNamespaceFromTypeName( $rc->getName() );

		if ( !isset( $this->namespaces[$rcNamespace] ) ) {
			$this->namespaces[ $rcNamespace ] =
				new Resource( ResourceTypes::NAMESPACE_RESOURCE, $rcNamespace, $namespaceRefs );
		} else {
			$this->namespaces[$rcNamespace]->addUsages( $namespaceRefs );
		}
	}

	/**
	 * @param string $typeName
	 *
	 * @return string
	 */
	private function getNamespaceFromTypeName( string $typeName ): string {
		if ( preg_match( '/^(.*)\\\\(\w+)$/', $typeName, $m ) ) {
			return $m[1];
		} else {
			return '\\';
		}
	}

}
