<?php
namespace SeanMorris\Kallisti\Test\Number;
class Agent extends \SeanMorris\Kallisti\Agent
{
	public function exchange()
	{
		return array_map(
			function($id)
			{
				return 'receiver';
			}
			, range(0x00, 0xFF)
		);
	}
}