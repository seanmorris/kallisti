<?php
require __DIR__ . '/../vendor/autoload.php';

function stdErr(...$args)
{
	fwrite(STDERR, sprintf(...$args) . PHP_EOL);
}

$tests = [
	'\SeanMorris\Kalisti\Test\Number'
	, '\SeanMorris\Kalisti\Test\Named'
	, '\SeanMorris\Kalisti\Test\Mixed'
];

array_map(
	function($namespace) {
		$testClass = $namespace . '\Test';
		$test = new $testClass;
		$test->run(new \TextReporter());
		echo PHP_EOL;
	}
	, $tests
);