<?php
namespace SeanMorris\Kallisti\Test\Mixed;
class Agent extends \SeanMorris\Kallisti\Agent
{
	public function exchange()
	{
		return ['model:*:#:*' => 'receiver'];
	}

	protected function receiver($content, &$output, $origin, $channel, $original)
	{
		list(,$type, $id, $property) = explode(':', $original);

		$output = $type::load($id);
		
		$output->{$property} = $content;
	}
}
