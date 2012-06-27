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
		$this->params = (array) $values;
	}

	/**
	 * @return bool
	 */
	public function hasParams() {
		return !empty($this->params);
	}

	/**
	 * @return array
	 */
	public function getParams() {
		return $this->params;
	}
}