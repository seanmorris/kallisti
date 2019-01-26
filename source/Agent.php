<?php
namespace SeanMorris\Kalisti;
class Agent
{
	private $id;
	private static $_id = 0;
	protected $hub, $expose = NULL, $context = [];

	public function __construct()
	{
		$this->id = self::$_id++;
	}

	public function onMessage($content, &$output, $origin, $channel, $originalChannel)
	{
		$exchange  = $this->exchange();
		$receivers = [];

		foreach($exchange as $channelSelector => $receiver)
		{
			if(FALSE !== $channel::compareNames(
				$channel->name
				, $channelSelector
			)){
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
			);
		}

		if($this->expose)
		{
			($this->expose)($content, $output, $origin, $channel, $originalChannel);
		}
	}

	protected function exchange()
	{
		return ['*' => 'receiver'];
	}

	protected function receiver($content, &$output, $origin, $channel, $originalChannel)
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
			return;
		}

		return $this->hub->publish($channel, $message, $this);
	}

	public function register($hub)
	{
		$exchange = $this->exchange();

		foreach($exchange as $channelSelector => $receiver)
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
