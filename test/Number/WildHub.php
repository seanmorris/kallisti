<?php
namespace SeanMorris\Kalisti\Test\Number;
class WildHub extends \SeanMorris\Kalisti\Hub
{
	public function channels()
	{
		return ['#' => 'SeanMorris\Kalisti\Channel'];
	}
}
