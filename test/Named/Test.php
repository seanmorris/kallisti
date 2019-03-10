<?php
namespace SeanMorris\Kallisti\Test\Named;
class Test extends \UnitTestCase
{
	function testRange()
	{
		$hub   = new \SeanMorris\Kallisti\Hub;
		$agent = new \SeanMorris\Kallisti\Test\Named\Agent;

		$agent->register($hub);

		$content = 'whoa!';

		$upper = $agent->send('upper', $content);

		$this->assertEqual(
			$upper
			, strtoupper($content)
			, 'UPPER channel returned incorrect result.'
		);

		$lower = $agent->send('lower', $content);

		$this->assertEqual(
			$lower
			, strtolower($content)
			, 'LOWER channel returned incorrect result.'
		);

		$initial = $agent->send('initial', $content);

		$this->assertEqual(
			$initial
			, ucwords($content)
			, 'INIT channel returned incorrect result.'
		);
	}
}
