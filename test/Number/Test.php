<?php
namespace SeanMorris\Kalisti\Test\Number;
class Test extends \UnitTestCase
{
	function testRange()
	{
		$hub   = new \SeanMorris\Kalisti\Test\Number\RangeHub;
		$agent = new \SeanMorris\Kalisti\Test\Number\Agent;

		$agent->register($hub);

		$agent->expose(
			function($content, &$output, $origin, $channel, $original) use(&$agent)
			{
				$output[] = sprintf(
					'%d::%s::%d::%d::%s'
					, $channel->name
					, $original
					, $origin->id
					, $agent->id
					, $content
				);
			}
		);

		$messages = ['0x0-0xF' => 'whoa!'];

		foreach($messages as $channelSelector => $message)
		{
			$result = $agent->send($channelSelector, $message);
			
			$this->assertEqual(
				count($result), 16
				, 'Incorrect number of return values for ranged transmission.'
			);
		}
	}

	function testWild()
	{
		$hub   = new \SeanMorris\Kalisti\Test\Number\WildHub;
		$agent = new \SeanMorris\Kalisti\Test\Number\Agent;

		$agent->register($hub);

		$agent->expose(
			function($content, &$output, $origin, $channel, $original) use(&$agent)
			{
				$output[] = sprintf(
					'%d::%s::%d::%d::%s'
					, $channel->name
					, $original
					, $origin->id
					, $agent->id
					, $content
				);
			}
		);

		$messages = array_map(
			function()
			{
				return 'whoa!';
			}
			, range(0x0, 0xFF)
		);

		foreach($messages as $channelSelector => $message)
		{
			$result = $agent->send($channelSelector, $message);

			$this->assertEqual(
				count($result), 1
				, 'Incorrect number of return values for single wildcard transmission.'
			);
		}

		$messages = ['0x0-0xFF' => 'whoa!'];

		foreach($messages as $channelSelector => $message)
		{
			$result = $agent->send($channelSelector, $message);

			$this->assertEqual(
				count($result), 256
				, 'Incorrect number of return values for wildcard ranged transmission.'
			);
		}
	}
}
