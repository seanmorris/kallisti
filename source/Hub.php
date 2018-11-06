<?php
namespace SeanMorris\Kalisti;
class Hub
{
	protected
		$agents         = []
		, $channels      = []
		, $subscriptions = [];

	public function channels()
	{
		return ['*' => 'SeanMorris\Kalisti\Channel'];
	}

	public function getChannels($name)
	{
		$channelClasses = $this->channels();

		if(($channelClasses[$name] ?? FALSE) && !($this->channels[$name] ?? FALSE))
		{
			if(!$channelClasses[$name]::isWildcard($name))
			{
				$this->channels[$name] = new $channelClasses[$name]($this, $name);
			}
		}

		if($this->channels[$name] ?? FALSE)
		{
			return [$name => $this->channels[$name]];
		}

		$channels = [];

		foreach($this->channels() as $channelName => $channelClass)
		{
			if(!$channelClass)
			{
				continue;
			}

			if(($comboName = $channelClass::compareNames($name, $channelName))!==FALSE)
			{
				if($range = $channelClass::deRange($comboName))
				{
					foreach($range as $numChannel)
					{
						$channels += $this->getChannels($numChannel);
					}
					continue;
				}
				else if($channelClass::isWildcard($comboName))
				{
					continue;
				}

				if(!isset($this->channels[$comboName]))
				{
					if($channelName == $comboName || $channelClass::create($comboName))
					{
						$this->channels[$comboName] = new $channelClass($this, $comboName);

						foreach($this->subscriptions as $agentId => $list)
						{
							foreach($list as $subChannelName => $isSubbed)
							{
								if($isSubbed && $channelClass::isWildcard($subChannelName))
								{
									$this->channels[$comboName]->subscribe($this->agents[$agentId]);

									$this->subscriptions[$agentId][$subChannelName] = TRUE;
								}
							}
						}
					}

				}

				if(isset($this->channels[$comboName]))
				{
					$channels[$comboName] = $this->channels[$comboName];
				}

			}
		}

		foreach($this->channels as $channelName => $channel)
		{
			if($channel::compareNames($name, $channelName))
			{
				$channels[$channelName] = $this->channels[$channelName];
			}
		}

		return $channels;
	}

	public function channelExists($name)
	{
		return $this->channels[$name] ?? FALSE;
	}

	public function subscribe($channelName, $agent)
	{
		$this->agents[$agent->id] = $agent;

		if($channels = $this->getChannels($channelName, $agent))
		{
			foreach($channels as $_channelName => $channel)
			{
				$channel->subscribe($agent);

				$this->subscriptions[$agent->id][$_channelName] = TRUE;
			}
		}

		$this->subscriptions[$agent->id][$channelName] = TRUE;
	}

	public function unsubscribe($channelName, $agent)
	{
		if($channels = $this->getChannels($channelName))
		{
			foreach($channels as $_channelName => $channel)
			{
				$channel->unsubscribe($agent);

				unset($this->subscriptions[$agent->id][$_channelName]);
			}

			unset($this->subscriptions[$agent->id]['*']);
		}

		unset($this->subscriptions[$agent->id][$channelName]);
	}

	public function subscriptions($agent)
	{
		return $this->subscriptions[$agent->id] ?? [];
	}

	public function publish($channelName, $content, $origin = NULL)
	{
		if(!$channels = $this->getChannels($channelName))
		{
			fwrite(STDERR, sprintf(
				"Channel %s does not exist!\n"
				, $channelName
			));

			return;
		}

		$output = NULL;

		foreach($channels as $channel)
		{
			$channel->send(
				$content
				, $output
				, $origin
				, $channelName
			);
		}

		return $output;
	}
}
