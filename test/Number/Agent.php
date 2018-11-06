<?php
namespace SeanMorris\Kalisti\Test\Number;
class Agent extends \SeanMorris\Kalisti\Agent
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