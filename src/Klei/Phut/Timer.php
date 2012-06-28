<?php
namespace Klei\Phut;

class Timer {
	protected $started;
	protected $elapsedTime;

	/**
	 * Starts or resets the timer
	 *
	 * @return void
	 */
	public function start() {
		$this->started = microtime(true);
		$this->elapsedTime = null;
	}

	/**
	 * Stops the timer
	 *
	 * @return void
	 */
	public function stop() {
		if (!$this->isRunning()) {
			throw new \Exception('The Timer must be running before it can be stopped. Run Timer::start().');
		}
		$this->elapsedTime = $this->getElapsedTime();
	}

	/**
	 * Checks if the current timer is running or not
	 *
	 * @return bool
	 */
	public function isRunning() {
		return (bool)($this->started !== null && $this->elapsedTime === null);
	}

	/**
	 * Checks if the current has been run
	 *
	 * @return bool
	 */
	public function hasBeenRun() {
		return (bool)($this->started !== null && $this->elapsedTime !== null);
	}

	/**
	 * Meassures the elapsed time and returns it.
	 * 
	 * If the timer is still running it meassures the elapsed time from when the timer started until now.
	 *
	 * @param int $precision If specified the elapsed time is rounded to this precision before it's returned
	 * @return float
	 */
	public function getElapsedTime($precision = null) {
		if ($this->started === null) {
			throw new \Exception('The Timer must be started before the elapsed time can be meassured. Run Timer::start().');
		}
		if ($this->elapsedTime === null) {
			$elapsed = (microtime(true) - $this->started);
		} else {
			$elapsed = $this->elapsedTime;
		}
		if ($precision !== null) {
			return round($elapsed, (int)$precision);
		}
		return $elapsed;
	}
}