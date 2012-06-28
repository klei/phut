<?php
namespace Klei\Phut;

use Doctrine\Common\Annotations\AnnotationReader;

class MethodHandler {
	protected $annotationReader;
	protected $annotationsNamespace = __NAMESPACE__;

	public function setAnnotationReader(AnnotationReader $annotationReader) {
        $this->annotationReader = $annotationReader;
    }

    public function getAnnotationReader() {
        if ($this->annotationReader == null) {
            $this->annotationReader = new AnnotationReader();
        }
        return $this->annotationReader;
    }

    public function getMethods($class) {
    	$reflectionClass = new \ReflectionClass($class);
    	return $reflectionClass->getMethods();
    }

    public function extractSetupMethod(array $methods) {
    	$result = array();
    	foreach ($methods as $method) {
    		if ($this->isSetupMethod($method)) {
    			$result[] = $method;
    		}
    	}
    	if (count($result) > 1) {
    		throw new \Exception(sprintf('There can be only one Setup method per class, %d found', count($result))); // @TODO: Move check to caller
    	}
    	if (isset($result[0])) {
    		return $result[0];
    	}
    	return null;
    }

    public function extractTestMethods(array $methods) {
    	$result = array();
    	foreach ($methods as $method) {
    		if ($this->isTestMethod($method)) {
    			$result[] = $method;
    		}
    	}
    	if (empty($result)) {
    		throw new \Exception('A TestFixture must have at least one Test method'); // @TODO: Move check to caller
    	}
    	return $result;
    }

    public function extractTeardownMethod(array $methods) {
    	$result = array();
    	foreach ($methods as $method) {
    		if ($this->isTeardownMethod($method)) {
    			$result[] = $method;
    		}
    	}
    	if (count($result) > 1) {
    		throw new \Exception(sprintf('There can be only one Teardown method per class, %d found', count($result))); // @TODO: Move check to caller
    	}
    	if (isset($result[0])) {
    		return $result[0];
    	}
    	return null;
    }

    public function isSetupMethod(\ReflectionMethod $method) {
    	// @TODO: Check that a setup method is not a test or a teardown
    	if ($this->getAnnotationReader()->getMethodAnnotation($method, $this->annotationsNamespace . '\Setup') instanceof Setup) {
			return true;
		}
		return false;
    }

    public function isTestMethod(\ReflectionMethod $method) {
    	// @TODO: Check that a test method is not a setup or a teardown
    	if ($this->getAnnotationReader()->getMethodAnnotation($method, $this->annotationsNamespace . '\Test') instanceof Test) {
			return true;
		}
		return false;
    }

    public function isTeardownMethod(\ReflectionMethod $method) {
    	// @TODO: Check that a teardown method is not a setup or a test
    	if ($this->getAnnotationReader()->getMethodAnnotation($method, $this->annotationsNamespace . '\Teardown') instanceof Teardown) {
			return true;
		}
		return false;
    }
}