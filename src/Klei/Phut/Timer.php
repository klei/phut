<?php
namespace Klei\Phut;

class Timer {
	protected $started;

	public function start() {
		$this->started = microtime(true);
	}

	public function stop($precision = null) {
		if ($this->started === null) {
			throw new \Exception('The Timer must be started before it can be stopped. Run Timer::start().');
		}
		$diff = (microtime(true) - $this->started);
		if ($precision !== null) {
			return round($diff, (int)$precision);
		}
		return $diff;
	}
}