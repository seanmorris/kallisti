<?php
require __DIR__ . '/../vendor/autoload.php';

function stdErr(...$args)
{
	fwrite(STDERR, sprintf(...$args) . PHP_EOL);
}

if(!$tests = array_slice($argv, 1))
{
	$tests = [
		'\SeanMorris\Kallisti\Test\Number'
		, '\SeanMorris\Kallisti\Test\Named'
		, '\SeanMorris\Kallisti\Test\Mixed'
	];
}

array_map(
	function($namespace) {
		$testClass = $namespace . '\Test';
		$test      = new $testClass;
		$test->run(new \TextReporter());
		echo PHP_EOL;
	}
	, $tests
);