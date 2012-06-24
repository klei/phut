<?php
namespace Klei\Phut;

use Symfony\Component\Finder\Finder;
use Doctrine\Common\Annotations\AnnotationReader;

class Runner {
	const DATE_FORMAT = 'Y-m-d H:i:s';
	const ELAPSED_TIME_FORMAT = '%7.3f';

	protected $arguments;
	protected $cli;
	protected $finder;
    protected $annotationReader;
    protected $binDir;
    protected $version;
    protected $annotationsNamespace = __NAMESPACE__;
    protected $totalTimer;

	public function __construct($arguments) {
		$this->checkBinPathConstant();
		$this->checkVersionConstant();
		$this->cli = new Cli();
		if (php_sapi_name() !== 'cli' || strtolower(substr(PHP_OS, 0, 3)) === 'win' && getenv('ANSICON') === null) {
			$this->cli->disableColoring();
		}
		$this->binDir = dirname(PHUT_BIN_PATH);
		$this->version = PHUT_VERSION;
		$this->arguments = $arguments;
		$this->totalTimer = new Timer();
	}

	public function checkBinPathConstant() {
		if (!defined('PHUT_BIN_PATH')) {
			throw new \Exception('Constant PHUT_BIN_PATH is not defined!');
		}
	}

	public function checkVersionConstant() {
		if (!defined('PHUT_VERSION')) {
			throw new \Exception('Constant PHUT_VERSION is not defined!');
		}
	}

	public function setFinder(Finder $finder) {
        $this->finder = $finder;
    }

    public function getFinder() {
        if ($this->finder == null) {
            $this->finder = new Finder();
        }
        return $this->finder;
    }

    public function setAnnotationReader(AnnotationReader $annotationReader) {
        $this->annotationReader = $annotationReader;
    }

    public function getAnnotationReader() {
        if ($this->annotationReader == null) {
            $this->annotationReader = new AnnotationReader();
        }
        return $this->annotationReader;
    }

    public function getDefaultTestFolder() {
    	return realpath($this->binDir . '/../tests');
    }

	public function run() {
		// Get all files in specified folder
		$files = $this->getFiles();

		// Cet all classes in the files
		$classes = $this->getClasses($files);

		// Get all classes marked with TestFixture annotation
		$testClasses = $this->getTestClasses($classes);

		// Get all methods in the test classes with Test annotation
		$testMethods = $this->getTestMethods($testClasses);

		// Print header
		$this->printHeader();

		// Run tests
		$this->runAllTests($testClasses, $testMethods);

		// Print footer
		$this->printFooter();
	}

	public function getFiles() {
		return $this->getFinder()
            ->files()
            ->name('*.php')
            ->in($this->getDefaultTestFolder());
	}

	public function getFullyQualifiedClassNameFromFile($fileName) {
		$content = file_get_contents($fileName);
		$tokens = token_get_all($content);

		$waitingForNamespace = false;
		$waitingForClass = false;

		$namespace = null;
		$class = null;
		foreach ($tokens as $token) {
			if (!is_array($token))
				continue;
			if ($token[0] == T_NAMESPACE) {
				$waitingForNamespace = true;
				$waitingForClass = false;
			} elseif ($token[0] == T_CLASS) {
				$waitingForClass = true;
				$waitingForNamespace = false;
			} elseif ($token[0] == T_STRING && $waitingForNamespace) {
				$namespace = $token[1];
				$waitingForNamespace = false;
			} elseif ($token[0] == T_STRING && $waitingForClass) {
				$class = $token[1];
				$waitingForClass = false;
			}
			if ($class !== null) {
				break;
			}
		}

		return $class !== null ? ($namespace !== null ? $namespace . '\\' : '') . $class : null;
	}

	protected function getClasses($files) {
		$foundClasses = array();

        foreach ($files as $file) {
            $class = $this->getFullyQualifiedClassNameFromFile($file->getPathname());
            if ($class !== null && $class !== '') {
            	$foundClasses[$file->getPathname()] = $class;
            }
        }

        return $foundClasses;
	}

	protected function getTestClasses($classes) {
		$testClasses = array();
		foreach ($classes as $fileName => $class) {
			if (!class_exists($class, false)) {
				include_once $fileName;
			}

			$reflectionClass = new \ReflectionClass($class);
			if ($this->isTestFixture($reflectionClass)) {
				$testClasses[] = $reflectionClass->getName();
			}
		}
		return $testClasses;
	}

	protected function isTestFixture(\ReflectionClass $class) {
		if ($this->getAnnotationReader()->getClassAnnotation($class, $this->annotationsNamespace . '\TestFixture') instanceof TestFixture) {
			return true;
		}
		return false;
	}

	protected function getTestMethods($testClasses) {
		$testMethods = array();
		foreach ($testClasses as $class) {
			$reflectionClass = new \ReflectionClass($class);
			foreach ($reflectionClass->getMethods() as $method) {
				// TODO: get Setup and Teardown methods here as well
				if ($this->isTestMethod($method)) {
					$testMethods[$class][] = $method;
				}
			}
		}
		return $testMethods;
	}

	protected function isTestMethod(\ReflectionMethod $method) {
		if ($this->getAnnotationReader()->getMethodAnnotation($method, $this->annotationsNamespace . '\Test') instanceof Test) {
			return true;
		}
		return false;
	}

	protected function runAllTests($testClasses, $testMethods) {
		// For each TestFixture
		foreach ($testClasses as $class) {
			if (!isset($testMethods[$class]))
				continue;

			echo PHP_EOL;

			$numberOfTests = count($testMethods[$class]);
			$fixtureTimer = new Timer();

			echo $this->cli->string(sprintf(' %s (%d tests)', $class, $numberOfTests), 'white') . PHP_EOL;

			$fixtureTimer->start();

			// Initiate TestFixture
			try {
				$testFixture = new $class();
			} catch (\Exception $e) {
				echo sprintf('   Error, TestFixture initialization failed with: %s', $e->getMessage()) . PHP_EOL;
			}

			$runTests = true;

			// Run SetUp-method if it exists (a method marked with Setup annotation)
			try {
				$this->runTestSetup($testFixture);
			} catch (AssertionException $ae) {
				echo sprintf('   %s', $ae->getMessage()) . PHP_EOL;
				$runTests = false;
			} catch (\Exception $e) {
				echo sprintf('   Error, Test Setup failed with: %s', $e->getMessage()) . PHP_EOL;
				$runTests = false;
			}

			// Run all its tests
			$numberOfFailed = 0;
			if ($runTests) {
				$testTimer = new Timer();

				foreach ($testMethods[$class] as $method) {
					echo sprintf("   - %-100s", $method->getName());

					$testTimer->start();

					// Run test and catch results
					$failed = false;
					$failMessage = null;
					try {
						$this->runTest($testFixture, $method);
					} catch (AssertionException $ae) {
						$failed = true;
						$failMessage = $ae->getMessage();
					} catch (\Exception $e) {
						$failed = true;
						$failMessage = sprintf('Error, Test failed with: %s', $e->getMessage());
					}

					echo sprintf(' %s %s', $this->getElapsedTimeString($testTimer), $this->getSuccessLabel($failed)) . PHP_EOL;

					if ($failed) {
						$numberOfFailed++;
						echo sprintf('       %s', $this->cli->string($failMessage, 'purple')) . PHP_EOL;
					}
				}
			} else {
				$numberOfFailed = $numberOfTests;
			}

			// Run TearDown-method if it exists (a method marked with Teardown annotation)
			try {
				$this->runTestTeardown($testFixture);
			} catch (AssertionException $ae) {
				echo sprintf('   %s', $ae->getMessage()) . PHP_EOL;
			} catch (\Exception $e) {
				echo sprintf('   Error, Test Teardown failed with: %s', $e->getMessage()) . PHP_EOL;
			}

			echo PHP_EOL;

			$resultMessage = sprintf(' %s failed, %s successful tests', $this->cli->string($numberOfFailed, 'red'), $this->cli->string($numberOfTests - $numberOfFailed, 'green'));
			$padding = str_pad(' ', 125 - strlen($resultMessage));
			echo sprintf(' %s %s %s %s', $resultMessage, $padding, $this->getElapsedTimeString($fixtureTimer), $this->getSuccessLabel($numberOfFailed > 0)) . PHP_EOL;
		}
	}

	protected function runTestSetup($testFixture) {

	}

	protected function runTest($class, $method) {
		$method->invoke($class);
	}

	protected function runTestTeardown($testFixture) {
		
	}

	protected function getElapsedTimeString(Timer $timer, $trim = false) {
		$elapsedTimeString = sprintf(self::ELAPSED_TIME_FORMAT . ' s', $timer->stop(3));
		if ($trim) {
			$elapsedTimeString = trim($elapsedTimeString);
		}
		return $this->cli->string($elapsedTimeString, 'dark-gray');
	}

	protected function getSuccessLabel($failed) {
		$failed = (bool)$failed;
		$color = $failed ? 'red' : 'green';
		$message = $failed ? 'FAIL' : ' OK ';
		return $this->cli->string('[' . $message . ']', $color);
	}

	protected function getNowFormatted() {
		$now = new \DateTime();
		return $now->format(self::DATE_FORMAT);
	}

	public function printHeader() {
		$this->totalTimer->start();

		echo PHP_EOL;
		echo $this->cli->string(' Phut, version: ' . $this->version, 'white') . PHP_EOL;
		echo ' -------------------------------' . PHP_EOL;
		echo PHP_EOL;
		echo sprintf(' Runner started at: %s', $this->getNowFormatted()) . PHP_EOL;
		echo PHP_EOL;
	}

	protected function printFooter() {
		echo PHP_EOL;
		echo sprintf(' Runner finished at: %s (%s)', $this->getNowFormatted(), $this->getElapsedTimeString($this->totalTimer, true)) . PHP_EOL;
	}
}
?>