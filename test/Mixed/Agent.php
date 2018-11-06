<?php
namespace SeanMorris\Kalisti\Test\Mixed;
class Agent extends \SeanMorris\Kalisti\Agent
{
	public function exchange()
	{
		return ['model:*:#:*' => 'receiver'];
	}

	protected function receiver($content, &$output, $origin, $channel, $original)
	{
		list(,$type, $id, $property) = explode(':', $channel->name);

		$output = $type::load($id);
		
		$output->{$property} = $content;
	}
}
