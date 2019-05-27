<?php
namespace SeanMorris\Kallisti;
class Hub
{
	protected
		$agents          = []
		, $channels      = []
		, $subscriptions = [];

	public function channels()
	{
		$channels = NULL;

		if(class_exists('SeanMorris\Ids\Settings'))
		{
			$channels = (array)\SeanMorris\Ids\Settings::read('kallisti', 'channels');
		}

		if($channels)
		{
			return $channels;
		}

		return ['*' => 'SeanMorris\Kallisti\Channel'];
	}

	public function getChannels($name, $reason = null)
	{
		fwrite(STDERR, sprintf(
			"Getting %s\n", $name
		));

		if($this->channels[$name] ?? FALSE)
		{
			if(!($this->channels[$name])::isWildcard($name))
			{
				return [$name => $this->channels[$name]];
			}
		}

		$channelClasses = $this->channels();

		if($channelClasses[$name] ?? FALSE)
		{
			if(!$channelClasses[$name]::isWildcard($name))
			{
				$this->channels[$name] = new $channelClasses[$name]($this, $name);
			}
		}

		$channels = [];

		foreach($this->channels() as $channelName => $channelClass)
		{
			if(!$channelClass)
			{
				continue;
			}

			if(($comboName = $channelClass::compareNames($channelName, $name))!==FALSE)
			{
				if($range = $channelClass::deRange($comboName))
				{
					foreach($range as $numChannel)
					{
						$channels += $this->getChannels($numChannel);
					}
					continue;
				}
				else if($channelClass::isRange($comboName))
				{
					continue;
				}

				if(!isset($this->channels[$comboName]))
				{
					if($channelName == $comboName || $channelClass::create($comboName))
					{
						if($reason !== 'publish')
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
				}

				if(isset($this->channels[$comboName]))
				{
					$channels[$comboName] = $this->channels[$comboName];
				}

			}
		}

		foreach($this->channels as $channelName => $channel)
		{
			if($channel::containsRange($channelName))
			{
				continue;
			}

			if($reason == 'publish' && $channel::isWildcard($name))
			{
				if(($comboName = $channel::compareNames($name, $channelName)) !== FALSE)
				{
					$channels[$comboName] = $this->channels[$comboName];
				}
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
		fwrite(STDERR, sprintf(
			"Subsscribing to %s!\n"
			, $channelName
		));
		$this->agents[$agent->id] = $agent;

		if($channels = $this->getChannels($channelName, 'subscribe'))
		{
			foreach($channels as $_channelName => $channel)
			{
				fwrite(STDERR, sprintf(
					"Sub to %s!\n"
					, $_channelName
				));
				if($channel->subscribe($agent) !== FALSE)
				{
					$this->subscriptions[$agent->id][$_channelName] = TRUE;
				}
			}
		}
	}

	public function unsubscribe($channelName, $agent)
	{
		if($channels = $this->getChannels($channelName))
		{
			$remove = [];

			foreach($channels as $_channelName => $channel)
			{
				$channel->unsubscribe($agent);

				unset($this->subscriptions[$agent->id][$_channelName]);

				if(!$channel->subscribers)
				{
					$remove[] = $_channelName;
				}
			}

			foreach($remove as $r)
			{
				unset($this->channels[$r]);
			}
		}

		unset($this->subscriptions[$agent->id][$channelName]);
	}

	public function subscriptions($agent)
	{
		return $this->subscriptions[$agent->id] ?? [];
	}

	public function publish($channelName, $content, $origin = NULL)
	{
		if(!$channels = $this->getChannels($channelName, 'publish'))
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

	public function say($channelName, $content, $origin = NULL, $cc = [], $bcc = [])
	{
		if(!$channels = $this->getChannels($channelName, 'publish'))
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
			$channel->say(
				$content
				, $output
				, $origin
				, $channelName
				, $cc
				, $bcc
			);
		}

		return $output;
	}

	public function listChannels()
	{
		return $this->channels;
	}
}
