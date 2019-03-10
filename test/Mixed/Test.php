<?php
namespace SeanMorris\Kallisti\Test\Mixed;
class Test extends \UnitTestCase
{
	function testRange()
	{
		$hub   = new \SeanMorris\Kallisti\Hub;
		$agent = new \SeanMorris\Kallisti\Test\Mixed\Agent;

		$agent->register($hub);

		$content = 'whoa!';

		foreach(range(0x0, 0xFF) as $id)
		{
			$result = $agent->send(
				sprintf(
					'model:SeanMorris\Kallisti\Test\Mixed\FakeModel:%d:title'
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
					'model:SeanMorris\Kallisti\Test\Mixed\FakeModel:%d:body'
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
				, 'Bodies do not match for model #' . $id
			);
		}
	}
}
