<?php
namespace Craft;

/**
 * Power Nap task
 */
class TasksCliTask extends BaseTask
{
	/**
	 * Defines the settings.
	 *
	 * @access protected
	 * @return array
	 */
	protected function defineSettings()
	{
		return ['howManySteps' => AttributeType::Number,];
	}

	/**
	 * Returns the default description for this task.
	 *
	 * @return string
	 */
	public function getDescription()
	{
		return 'Creating a test task';
	}

	/**
	 * Gets the total number of steps for this task.
	 *
	 * @return int
	 */
	public function getTotalSteps()
	{
		//var_dump($this->getSettings()->howManySteps);
		return 20;
	}

	/**
	 * Runs a task step.
	 *
	 * @param int $step
	 * @return bool
	 */
	public function runStep($step)
	{


		sleep(.2);
		return true;
	}
}