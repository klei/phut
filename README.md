Phut
======

**Phut** is a small unit test framework for PHP 5.3+, inspired by [NUnit](http://nunit.org)

What is Phut
--------------

Phunit is a **PH**P **U**nit **T**est framework which uses annotations to mimic the behavior of NUnit for .Net.

Why another unit testing framework?
-----------------------------------

I was tired searching for a unit testing framework for PHP that:

  * Fully supports namespaces
  * Is easy to setup
  * Works on Windows without a hassle

So finally, when I didn't find a single framework that did all of the above, I decided to do it myself.

Installation
---------------

You can install **Phut** using one of the following methods.

### Using Composer

One simple way to install Phut is to use [Composer](http://getcomposer.org/). First create or modify your ```composer.json``` file in the project root to include:

```json
{
    "require": {
        "klei\phut": "*"
    },
    "config": {
        "bin-dir": "bin/"
    }
}
```

Then download ```composer.phar``` from http://getcomposer.org/ and run:

    php composer.phar install

### Or using git

You could also clone the Phut repository with:

    git clone git://github.com/klei-dev/phut.git

Then download composer.phar as above and execute the following:

    php composer.phar install


Writing your first test
-----------------------

As mentioned above Phut uses annotations to work. The name of the annotations is directly inspired by the NUnit framework for .Net. I.e. a test class must be annotated with ```@TestFixture``` and each test with ```@Test```.

A simple test could therefore look like this:

```php
<?php
namespace vendor\tests;

use Klei\Phut\TestFixture;
use Klei\Phut\Test;
use Klei\Phut\Assert;

/**
 * @TestFixture
 */
class MyFirstTests {
	/**
	 * @Test
	 */
	public function MultiplyOperator_Multiply2by4_ShouldGive8()
	{
		// Given
		$number1 = 2;
		$number2 = 4;

		// When
		$result = $number1 * $number2;

		// Then
		Assert::areIdentical($result, 8);
	}
}
```

You can then run your test with the command:

    bin/phut [<test-folder-name>|<test-file-name>]

If no folder (```<test-folder-name>```) or file (```<test-file-name>```) is specified, the runner defaults to the folder ```tests/```.

The future for Phut
-------------------

Stuff for future releases:

* More extensive Assert-class
* ```@TestCase``` annotation to write parameterized tests
* Optional ```Category``` parameter for the ```@Test``` annotation to be able to categorize tests and run only certain categories

Copyright
---------

Copyright © 2012, Joakim Bengtson. See LICENSE.

Questions?
----------

You can find me [here at GitHub](http://github.com/joakimbeng) and via twitter at [@joakimbeng](http://twitter.com/joakimbeng).