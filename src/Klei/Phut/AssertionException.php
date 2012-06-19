<?php
namespace Klei\Phut;

class AssertionException extends \Exception {
	public function __construct($message, $code = 0) {
		parent::__construct($message, $code);
	}
}
?>
