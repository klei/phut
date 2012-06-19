<?php
namespace Klei\Phut;

use Symfony\Component\Finder\Finder;
use Doctrine\Common\Annotations\AnnotationReader;

class Runner {
	protected $arguments;
	protected $finder;
    protected $annotationReader;
    protected $binDir;
    protected $version;
    protected $annotationsNamespace = __NAMESPACE__;
    protected $totalTimer;

	public function __construct($arguments) {
		if (!defined('PHUT_BIN_PATH')) {
			throw new \Exception('Constant PHUT_BIN_PATH is not defined!');
		}
		if (!defined('PHUT_VERSION')) {
			throw new \Exception('Constant PHUT_VERSION is not defined!');
		}
		$this->binDir = dirname(PHUT_BIN_PATH);
		$this->version = PHUT_VERSION;
		$this->arguments = $arguments;
		$this->totalTimer = new Timer();
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
			if ($this->getAnnotationReader()->getClassAnnotation($reflectionClass, $this->annotationsNamespace . '\TestFixture') instanceof TestFixture) {
				$testClasses[] = $reflectionClass->getName();
			}
		}
		return $testClasses;
	}

	protected function getTestMethods($testClasses) {
		$testMethods = array();
		foreach ($testClasses as $class) {
			$reflectionClass = new \ReflectionClass($class);
			foreach ($reflectionClass->getMethods() as $method) {
				// TODO: get Setup and Teardown methods here as well
				if ($this->getAnnotationReader()->getMethodAnnotation($method, $this->annotationsNamespace . '\Test') instanceof Test)
					$testMethods[$class][] = $method;
			}
		}
		return $testMethods;
	}

	protected function runAllTests($testClasses, $testMethods) {
				// For each TestFixture
		foreach ($testClasses as $class) {
			if (!isset($testMethods[$class]))
				continue;

			echo $class . ' (' . count($testMethods[$class]) . ' tests)' . PHP_EOL;

			// Initiate TestFixture
			try {
				$testFixture = new $class();
			} catch (\Exception $e) {
				echo "\tError, TestFixture initialization failed with: {$e->getMessage()}" . PHP_EOL;
			}

			$runTests = true;

			// Run SetUp-method if it exists (a method marked with Setup annotation)
			try {
				$this->runTestSetup($testFixture);
			} catch (AssertionException $ae) {
				echo "\t" . $ae->getMessage() . PHP_EOL;
				$runTests = false;
			} catch (\Exception $e) {
				echo "\tError, Test Setup failed with: {$e->getMessage()}" . PHP_EOL;
				$runTests = false;
			}

			// Run all its tests
			if ($runTests) {
				$testTimer = new Timer();

				foreach ($testMethods[$class] as $method) {
					echo "\t- {$method->getName()}";

					$testTimer->start();

					// Run test and catch results
					try {
						$this->runTest($testFixture, $method);
						echo "\t\t" . $testTimer->stop(3) . ' s [ OK ]' . PHP_EOL;
					} catch (AssertionException $ae) {
						echo "\t\t" . $testTimer->stop(3) . ' s [FAIL]' . PHP_EOL;
						echo "\t\t" . $ae->getMessage() . PHP_EOL;
					} catch (\Exception $e) {
						echo "\t\t" . $testTimer->stop(3) . ' s [FAIL]' . PHP_EOL;;
						echo "\t\tError, Test failed with: {$e->getMessage()}" . PHP_EOL;
					}
				}
			}

			// Run TearDown-method if it exists (a method marked with Teardown annotation)
			try {
				$this->runTestTeardown($testFixture);
			} catch (AssertionException $ae) {
				echo "\t" . $ae->getMessage() . PHP_EOL;
			} catch (\Exception $e) {
				echo "\tError, Test Teardown failed with: {$e->getMessage()}" . PHP_EOL;
			}
		}
	}

	protected function runTestSetup($testFixture) {

	}

	protected function runTest($class, $method) {
		$method->invoke($class);
	}

	protected function runTestTeardown($testFixture) {
		
	}

	public function printHeader() {
		$now = new \DateTime();
		$this->totalTimer->start();
		echo PHP_EOL;
		echo 'Phut, version: ' . $this->version . PHP_EOL;
		echo '-------------------------------' . PHP_EOL;
		echo PHP_EOL;
		echo 'Runner started at: ' . $now->format('Y-m-d H:i:s') . PHP_EOL;
		echo PHP_EOL;
	}

	protected function printFooter() {
		$now = new \DateTime();
		echo PHP_EOL;
		echo "Runner finished at: " . $now->format('Y-m-d H:i:s') . " (" . $this->totalTimer->stop(3) . " s)" . PHP_EOL;
	}
}
?>