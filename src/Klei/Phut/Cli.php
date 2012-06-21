<?php
namespace Klei\Phut;

class Cli {
	protected $foreground = array(
		'black' => '0;30',
		'dark-gray' => '1;30',
		'red' => '0;31',
		'light-red' => '1;31',
		'green' => '0;32',
		'light-green' => '1;32',
		'brown' => '0;33',
		'yellow' => '1;33',
		'blue' => '0;34',
		'light-blue' => '1;34',
		'purple' => '0;35',
		'light-purple' => '1;35',
		'cyan' => '0;36',
		'light-cyan' => '1;36',
		'light-gray' => '0;37',
		'white' => '1;37'
	);

	protected $background = array(
		'black' => '40',
		'red' => '41',
		'green' => '42',
		'yellow' => '43',
		'blue' => '44',
		'magenta' => '45',
		'cyan' => '46',
		'light-gray' => '47'
	);

	protected $reset = '0';

	public function string($string, $foregroundColor = null, $backgroundColor = null) {
		$result = "";

		if ($foregroundColor !== null) {
			$result .= $this->color($this->getForegroundColor($foregroundColor));
		}

		if ($backgroundColor !== null) {
			$result .= $this->color($this->getBackgroundColor($backgroundColor));
		}

		$result .= $string;

		if ($foregroundColor !== null || $backgroundColor !== null) {
			$result .= $this->color($this->reset);
		}

		return $result;
	}

	public function color($color) {
		return "\033[" . $color . "m";
	}

	protected function getForegroundColor($foregroundColor) {
		if (!isset($this->foreground[$foregroundColor])) {
			throw new \InvalidArgumentException(sprintf('The specified foreground color \'%s\' does not exist', $foregroundColor));
		}
		return $this->foreground[$foregroundColor];
	}

	protected function getBackgroundColor($backgroundColor) {
		if (!isset($this->background[$backgroundColor])) {
			throw new \InvalidArgumentException(sprintf('The specified background color \'%s\' does not exist', $backgroundColor));
		}
		return $this->background[$backgroundColor];
	}
}
?>