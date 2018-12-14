<?php
namespace SeanMorris\Kalisti;
class Channel
{
	const SEPARATOR = ':';
	protected
		$server
		, $name
		, $subscribers = []
	;

	public function __construct($server, $name)
	{
		$this->server = $server;
		$this->name   = $name;
	}

	public static function isWildcard($name)
	{
		return preg_match('/\*/', $name)
			|| static::isRange($name)
			|| static::containsRange($name)
		;
	}

	public static function isRange($name)
	{
		return preg_match('/^0?x?[\dA-F]+-0?x?[\dA-F]+$/', $name);
	}

	public static function containsRange($name)
	{
		return preg_match('/0?x?[\dA-F]+-0?x?[\dA-F]+/', $name);
	}

	public static function deRange($name)
	{
		$split  = explode(static::SEPARATOR, $name);
		$format = [];
		$names  = [];

		foreach($split as $segment)
		{
			if(!static::isRange($segment))
			{
				array_push($format, $segment);
				continue;
			}

			array_push($format, '%d');

			if(!$names)
			{
				list($start, $end) = explode('-', $segment);
				$_format = join(static::SEPARATOR, $format);

				foreach(range($start, $end) as $i)
				{
					$names[] = sprintf($_format, $i);
				}
			}
			else
			{
				$_names = $names;
				$names  = [];
				list($start, $end) = explode('-', $segment);
				foreach($_names as $partialName)
				{
					$_format = $partialName
						. static::SEPARATOR
						. join(static::SEPARATOR, $format);

					foreach(range($start, $end) as $i)
					{
						$names[] = sprintf($_format, $i);
					}
				}
			}

			$format = [];
		}

		return $names;
	}

	protected static function deHex($string)
	{
		$regex = '/0x([\dA-F]+)/i';

		while(preg_match($regex, $string, $groups))
		{
			$string = preg_replace($regex, hexdec($groups[1]), $string, 1);
		}

		return $string;
	}

	public static function compareNames($a, $b)
	{
		$a = static::deHex($a);
		$b = static::deHex($b);

		$result = [];
		$splitA = explode(static::SEPARATOR, $a);
		$splitB = explode(static::SEPARATOR, $b);
		$countA = count($splitA);
		$countB = count($splitB);
		$nodes  = $countA;

		if($nodes < $countB)
		{
			$nodes = $countB;
		}

		for($i = 0; $i < $nodes; $i++)
		{
			if(count($splitA) > $i)
			{
				$cmpA = $splitA[$i];
			}
			else if($splitA[ $countA - 1] == '*')
			{
				$cmpA = $splitA[ $countA - 1];
			}
			else
			{
				return FALSE;
			}

			if(count($splitB) > $i)
			{
				$cmpB = $splitB[$i];
			}
			else if($splitB[ $countB - 1] == '*')
			{
				$cmpB = $splitB[ $countB - 1];
			}
			else
			{
				return FALSE;
			}

			$returnNode = $cmpA !== '*' ? $cmpA : $cmpB;

			if($cmpA !== $cmpB)
			{
				if(intval($cmpA) == $cmpA && $cmpB == '#')
				{
					$returnNode = $cmpB;
					if($cmpA !== '*')
					{
						$returnNode = $cmpA;
					}
				}
				else if(intval($cmpB) == $cmpB && $cmpA == '#')
				{
					$returnNode = $cmpA;
					if($cmpB !== '*')
					{
						$returnNode = $cmpB;
					}
				}	
				else if($cmpA !== '*' && $cmpB !== '*')
				{
					$rangeForm = '/^(\d+)+\-?(\d+)?$/';

					$mA = preg_match($rangeForm, $cmpA, $groupA);
					$mB = preg_match($rangeForm, $cmpB, $groupB);

					if($mA && $mB)
					{
						$a1 = $groupA[1];
						$a2 = $groupA[1];
						$b1 = $groupB[1];
						$b2 = $groupB[1];

						if(isset($groupA[2]))
						{
							$a2 = $groupA[2];
						}

						if(isset($groupB[2]))
						{
							$b2 = $groupB[2];
						}

						if($a1 >= $b1 && $a2 <= $b2)
						{
							$returnNode = "$a1-$a2";

							if($a1 == $a2)
							{
								$returnNode = (int) $a1;
							}
						}
						else if($a1 <= $b1 && $a2 >= $b2)
						{
							$returnNode = "$b1-$b2";

							if($b1 == $b2)
							{
								$returnNode = (int) $b2;
							}
						}
						else if($a2 <= $b2 && $a2 >= $b1)
						{
							$returnNode = "$b1-$a2";

							if($b1 == $a2)
							{
								$returnNode = (int) $b1;
							}
						}
						else if($a1 <= $b2 && $a1 >= $b1)
						{
							$returnNode = "$a1-$b2";

							if($a1 == $b2)
							{
								$returnNode = (int) $a1;
							}
						}
						else if($b2 <= $a2 && $b2 >= $a1)
						{
							$returnNode = "$a1-$b2";

							if($a1 == $b2)
							{
								$returnNode = (int) $a1;
							}
						}
						else if($b1 <= $a2 && $b1 >= $a1)
						{
							$returnNode = "$b1-$a2";

							if($b1 == $a2)
							{
								$returnNode = (int) $b1;
							}
						}
						else
						{
							return FALSE;
						}
					}
					else
					{
						return FALSE;
					}

				}
			}

			$result[] = $returnNode;
		}

		if(!$result)
		{
			return FALSE;
		}

		return implode(static::SEPARATOR, $result);
	}

	public function subscribe($agent)
	{
		foreach($this->subscribers as $index => $subscriber)
		{
			if($subscriber === $agent)
			{
				return;
			}
		}

		$this->subscribers[] = $agent;
	}

	public function unsubscribe($agent)
	{
		foreach($this->subscribers as $index => $subscriber)
		{
			if($subscriber === $agent)
			{
				unset($this->subscribers[$index]);
			}
		}
	}

	public function send($content, &$output, $origin, $originalChannel = NULL)
	{
		foreach($this->subscribers as $agent)
		{
			$agent->onMessage(
				$content
				, $output
				, $origin
				, $this
				, $originalChannel
			);
		}
	}

	public static function create($name)
	{
		return TRUE;
	}

	public function __get($name)
	{
		return $this->$name;
	}
}
