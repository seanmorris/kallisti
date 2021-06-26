<?php
namespace SeanMorris\Kallisti;
class Agent
{
	private $id;
	private static $_id = 0;
	protected $hub, $expose = NULL, $context = [];

	public function __construct()
	{
		$this->id = self::$_id++;
	}

	public function onMessage($content, &$output, $origin, $channel, $originalChannel, $cc = NULL)
	{
		$channelMap  = $this->channelMap();
		$receivers = [];

		foreach($channelMap as $channelSelector => $receiver)
		{
			if($channel::compareNames($channel->name, $channelSelector) !== FALSE)
			{
				$receivers[] = $receiver;
			}
		}

		$receivers = array_unique($receivers);

		foreach($receivers as $receiver)
		{
			$this->{$receiver}(
				$content
				, $output
				, $origin
				, $channel
				, $originalChannel
				, $cc
			);
		}

		if($this->expose)
		{
			($this->expose)($content, $output, $origin, $channel, $originalChannel, $cc);
		}
	}

	protected function channelMap()
	{
		return ['*' => 'receiver'];
	}

	protected function receiver($content, &$output, $origin, $channel, $originalChannel, $cc)
	{
	}

	public function __get($name)
	{
		return $this->$name;
	}

	public function expose(Callable $callback = NULL)
	{
		$this->expose = $callback;
	}

	public function send($channel, $message)
	{
		if(!$this->hub)
		{
			throw new Exception('No hub registered to Agent!');
		}

		return $this->hub->publish($channel, $message, $this);
	}

	public function register($hub)
	{
		$channelMap = $this->channelMap();

		foreach($channelMap as $channelSelector => $receiver)
		{
			$hub->subscribe($channelSelector, $this);
		}

		$this->hub = $hub;
	}

	public function &getContext()
	{
		return $this->context;
	}

	public function setContext(&$context)
	{
		$this->context =& $context;
	}

	public function contextGet($name, $default = NULL)
	{
		if(isset($this->context[$name]))
		{
			return $this->context[$name];
		}

		return $default;
	}

	public function contextSet($name, $value)
	{
		$this->context[$name] = $value;
	}
}
