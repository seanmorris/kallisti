<?php
namespace SeanMorris\Kallisti\Test\Number;
class RangeHub extends \SeanMorris\Kallisti\Hub
{
	public function channels()
	{
		return array_map(
			function($id)
			{
				return 'SeanMorris\Kallisti\Channel';
			}
			, range(0x0, 0xF)
		);
	}
}