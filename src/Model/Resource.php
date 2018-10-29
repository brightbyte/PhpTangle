<?php
/**
 * This file is part of PhpTangle.
 *
 * @license LGPL 2+
 * @author Daniel Kinzler
 */

namespace Wikimedia\PhpTangle\Model;

/**
 * Represents a resource that may be used, and may use other resources.
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
	 * @param string $type
	 * @param string $name
	 * @param string[] $usages
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
	 * @return string[]
	 */
	public function getUsages(): array {
		return $this->usages;
	}

}
