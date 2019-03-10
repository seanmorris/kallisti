<?php
namespace SeanMorris\Kallisti\Test\Number;
class WildHub extends \SeanMorris\Kallisti\Hub
{
	public function channels()
	{
		return ['#' => 'SeanMorris\Kallisti\Channel'];
	}
}
