<?php
namespace Klei\Phut;

use Doctrine\Common\Annotations\Annotation as DoctrineAnnotation;

/**
 * @Annotation
 * @DoctrineAnnotation\Target("METHOD")
 */
final class TestCase {
	/**
	 * @var array
	 */
	protected $params;

	public function __construct($values) {

	}

	/**
	 * @return bool
	 */
	public function hasParams() {
		return true; // @TODO
	}

	/**
	 * @return array
	 */
	public function getParams() {
		return array("TODO"); // @TODO
	}
}