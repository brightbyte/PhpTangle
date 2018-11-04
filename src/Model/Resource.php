<?php
/**
 * This file is part of PhpTangle.
 *
 * @license LGPL 2+
 * @author Daniel Kinzler
 */

namespace Wikimedia\PhpTangle\Model;

/**
 * Value object representing a resource that may be used, and may use other resources.
 * A Resource represents a node in the usages graph, along with its outgoing edges.
 */
class Resource {

	/** @var string */
	private $type;

	/** @var string */
	private $name;

	/** @var string[] */
	private $usages;

	/**
	 * Resource constructor.
	 *
	 * @param string $type The type of the resource, using a ResourceType constant.
	 * @param string $name
	 * @param Reference[] $usages
	 */
	public function __construct( string $type, string $name, array $usages ) {
		$this->type = $type;
		$this->name = $name;
		$this->usages = $usages;
	}

	/**
	 * @return string
	 */
	public function getType(): string {
		return $this->type;
	}

	/**
	 * @return string
	 */
	public function getName(): string {
		return $this->name;
	}

	/**
	 * @return Reference[]
	 */
	public function getUsages(): array {
		return $this->usages;
	}

	/**
	 * @param Reference[] $usages
	 */
	public function addUsages( array $usages ) {
		$this->usages = array_merge( $this->usages, $usages );
	}

	/**
	 * @return string
	 */
	public function __toString() {
		return $this->getType() . ' ' . $this->getName();
	}

}
