<?php
namespace SeanMorris\Kalisti\Test\Mixed;
class Test extends \UnitTestCase
{
	function testRange()
	{
		$hub   = new \SeanMorris\Kalisti\Hub;
		$agent = new \SeanMorris\Kalisti\Test\Mixed\Agent;

		$agent->register($hub);

		$content = 'whoa!';

		foreach(range(0x0, 0xFF) as $id)
		{
			$result = $agent->send(
				sprintf(
					'model:SeanMorris\Kalisti\Test\Mixed\FakeModel:%d:title'
					, $id
				)
				, $content
			);

			$this->assertEqual(
				$content, $result->title
				, 'Titles do not match for model #' . $id
			);
		}

		foreach(range(0x0, 0xFF) as $id)
		{
			$result = $agent->send(
				sprintf(
					'model:SeanMorris\Kalisti\Test\Mixed\FakeModel:%d:body'
					, $id
				)
				, $content
			);

			$this->assertEqual(
				$content, $result->title
				, 'Titles do not match for model #' . $id
			);

			$this->assertEqual(
				$content, $result->body
				, 'Titles do not match for model #' . $id
			);
		}
	}
}
