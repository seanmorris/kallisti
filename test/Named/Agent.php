<?php
namespace SeanMorris\Kalisti\Test\Named;
class Agent extends \SeanMorris\Kalisti\Agent
{
	public function exchange()
	{
		return [
			'upper'     => 'uppercase'
			, 'lower'   => 'lowercase'
			, 'initial' => 'initialCaps'
		];
	}

	protected function uppercase($content, &$output, $origin, $channel)
	{
		if($output)
		{
			$content = $output;
		}
		
		$output = strtoupper($content);
	}

	protected function lowercase($content, &$output, $origin, $channel)
	{
		if($output)
		{
			$content = $output;
		}
		
		$output = strtolower($content);
	}

	protected function initialCaps($content, &$output, $origin, $channel)
	{
		if($output)
		{
			$content = $output;
		}
		
		$output = ucwords($content);
	}
}