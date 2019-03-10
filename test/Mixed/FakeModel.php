<?php
namespace SeanMorris\Kallisti\Test\Mixed;
class FakeModel
{
	static $cache;

	public static function load($id)
	{
		if(isset(static::$cache[$id]))
		{
			return static::$cache[$id];
		}

		$static = new static;

		$static->id = $id;

		return static::$cache[$id] = $static;
	}

	protected function __construct(){}
}
