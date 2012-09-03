<?php
namespace Klei\Phut;

use ReflectionMethod;
use ReflectionClass;
use Doctrine\Common\Annotations\AnnotationReader;

class MethodHandler {
    protected $annotationReader;
    protected $annotationsNamespace = __NAMESPACE__;

    public function setAnnotationReader(AnnotationReader $annotationReader) {
        $this->annotationReader = $annotationReader;
    }

    public function getAnnotationReader() {
        if ($this->annotationReader == null) {
            $this->setAnnotationReader(new AnnotationReader());
        }
        return $this->annotationReader;
    }

    public function getMethods($class) {
        $reflectionClass = new ReflectionClass($class);
        return $reflectionClass->getMethods();
    }

    public function extractSetupMethod(array $methods) {
        $result = array_filter($methods, function ($method) {
            return $this->isSetupMethod($method);
        });
        if (count($result) > 1) {
            throw new \Exception(sprintf('There can be only one Setup method per class, %d found', count($result))); // @TODO: Move check to caller
        }
        if (($setupMethod = current($result)) !== false) {
            return $setupMethod;
        }
        return null;
    }

    public function extractTestMethods(array $methods) {
        $result = array_filter($methods, function ($method) {
            return $this->isTestMethod($method);
        });
        if (empty($result)) {
            throw new \Exception('A TestFixture must have at least one Test method'); // @TODO: Move check to caller
        }
        return $result;
    }

    public function extractTeardownMethod(array $methods) {
        $result = array_filter($methods, function ($method) {
            return $this->isTeardownMethod($method);
        });
        if (count($result) > 1) {
            throw new \Exception(sprintf('There can be only one Teardown method per class, %d found', count($result))); // @TODO: Move check to caller
        }
        if (($teardownMethod = current($result)) !== false) {
            return $teardownMethod;
        }
        return null;
    }

    public function isSetupMethod(ReflectionMethod $method) {
        return $this->isMethodOfType($method, 'Setup');
    }

    public function isTestMethod(ReflectionMethod $method) {
        return $this->isMethodOfType($method, 'Test');
    }

    public function isTeardownMethod(ReflectionMethod $method) {
        return $this->isMethodOfType($method, 'Teardown');
    }

    protected function isMethodOfType(ReflectionMethod $method, $type)
    {
        $annotationClass = $this->getAnnotationClassName($type);
        if ($this->getAnnotationReader()->getMethodAnnotation($method, $annotationClass) instanceof $annotationClass) {
            return true;
        }
        return false;
    }

    protected function getAnnotationClassName($type)
    {
        return $this->annotationsNamespace . '\\' . $type;
    }
}