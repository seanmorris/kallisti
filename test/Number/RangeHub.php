<?php
namespace SeanMorris\Kalisti\Test\Number;
class RangeHub extends \SeanMorris\Kalisti\Hub
{
	public function channels()
	{
		return array_map(
			function($id)
			{
				return 'SeanMorris\Kalisti\Channel';
			}
			, range(0x0, 0xF)
		);
	}
}