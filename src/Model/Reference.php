<?php
/**
 * This file is part of PhpTangle.
 *
 * @license LGPL 2+
 * @author Daniel Kinzler
 */

namespace Wikimedia\PhpTangle\Model;

/**
 * Value object representing a reference to a resource, that is, a usage of a resource.
 */
class Reference {

	const LOCAL_VISIBILITY = 'local';
	const PRIVATE_VISIBILITY = 'private';
	const PROTECTED_VISIBILITY = 'protected';
	const PUBLIC_VISIBILITY = 'public';
	const INTERNAL_VISIBILITY = 'internal';
	const DECLARED_VISIBILITY = 'declared';

	const USE_MODE = 'use';
	const HINT_MODE = 'hint';
	const INSTANTIATION_MODE = 'instantiation';
	const LITERAL_MODE = 'literal';
	const SUCLASSING_MODE = 'subclassing';
	const MIXIN_MODE = 'mixin';
	const INCLUDE_MODE = 'include';

	/**
	 * @var string
	 */
	private $visibility;

	/**
	 * @var string
	 */
	private $mode;

	/**
	 * @var string
	 */
	private $rcType;

	/**
	 * @var string
	 */
	private $rcName;

	/**
	 * Reference constructor.
	 *
	 * @param string $visibility
	 * @param string $mode
	 * @param string $rcType
	 * @param string $rcName
	 */
	public function __construct( $visibility, $mode, $rcType, $rcName ) {
		$this->visibility = $visibility;
		$this->mode = $mode;
		$this->rcType = $rcType;
		$this->rcName = $rcName;
	}

	/**
	 * @return string
	 */
	public function getVisibility(): string {
		return $this->visibility;
	}

	/**
	 * @return string
	 */
	public function getMode(): string {
		return $this->mode;
	}

	/**
	 * @return string
	 */
	public function getResourceType(): string {
		return $this->rcType;
	}

	/**
	 * @return string
	 */
	public function getResourceName(): string {
		return $this->rcName;
	}

	/**
	 * @return string
	 */
	public function __toString() {
		return $this->getVisibility() . ' ' . $this->getMode() . ' of ' . $this->getResourceType() . ' ' .
			$this->getResourceName();
	}

}
