<?php
namespace Craft;

class TasksCliService extends TasksService
{
    /**
     * @var
     */
    private $_taskRecordsById;

    /**
     * @var
     */
    private $_nextPendingTask;

    /**
     * @var
     */
    private $_runningTask;

    /**
     * @var
     */
    private $_listeningForRequestEnd = false;

    public function runTask(TaskModel $task)
    {


        $error = null;

        try
        {
            $taskRecord = $this->_getTaskRecordById($task->id);
            $taskType = $task->getTaskType();

            if ($taskType)
            {
                // Figure out how many total steps there are.
                $task->totalSteps = $taskType->getTotalSteps();
                $task->status = TaskStatus::Running;
                $stepsPerBatch = craft()->config->get('stepsPerBatch', 'taskscli');
                echo 'Starting task '.$taskRecord->type.' that has a total of '.max(1,$task->totalSteps).' steps, with '. $stepsPerBatch.' steps per batch'  . PHP_EOL;

                $batches = ceil($task->totalSteps/$stepsPerBatch);


                $task->currentStep ?: 0;
                $this->saveTask($task);


                //for ($step = 0; $step < $task->totalSteps; $step++)
                for ($batch = 0; $batch < $batches; $batch++)
                {
                    echo 'Starting batch '.($batch+1).' of '.$batches.' total batches. (Memory: '.round(memory_get_usage() / 1024 / 1024, 2).' MB)'  . PHP_EOL;
                    $command = implode('', [
                        'php ',
                        CRAFT_APP_PATH.'/etc/console/yiic',
                        ' taskscli runTaskBatch',
                        " --taskId={$task->id}",
                    ]);
                    passthru($command, $result);

                    // Run it.
                    if ($result != "true")
                    {
                        // Did they give us an error to report?
                        if (is_string($result))
                        {
                            $error = $result;
                        }
                        else
                        {
                            $error = true;
                        }

                        break;
                    }
                }
            }
            else
            {
                $error = 'Could not find the task component type.'.PHP_EOL;
            }
        }
        catch (\Exception $e)
        {
            $error = 'An exception was thrown: '.$e->getMessage();
        }

        if ($task == $this->_nextPendingTask)
        {
            // Don't run this again
            $this->_nextPendingTask = null;
        }

        if ($error === null)
        {
            echo 'Finished task '.$task->id.' ('.$task->type.').'.PHP_EOL;

            // We're done with this task, nuke it.
            $taskRecord->deleteNode();

            return true;
        }
        else
        {
            $this->fail($task, $error);
            return false;
        }
    }

    /**
     * Returns the next pending task.
     *
     * @param string|null $type
     *
     * @return TaskModel|null|false
     */
    public function getNextPendingTask($type = null)
    {
        // If a type was passed, we don't need to actually save it, as it's probably not an actual task-running request.
        if ($type)
        {
            $pendingTasks = $this->getPendingTasks($type, 1);

            if ($pendingTasks)
            {
                return $pendingTasks[0];
            }
        }
        else
        {
            if (!isset($this->_nextPendingTask))
            {
                $taskRecord = TaskRecord::model()->roots()->ordered()->findByAttributes(array(
                    'status' => TaskStatus::Pending
                ));

                if ($taskRecord)
                {
                    $this->_taskRecordsById[$taskRecord->id] = $taskRecord;
                    $this->_nextPendingTask = TaskModel::populateModel($taskRecord);
                }
                else
                {
                    $this->_nextPendingTask = false;
                }
            }

            if ($this->_nextPendingTask)
            {
                return $this->_nextPendingTask;
            }
        }
    }

    private function _getTaskRecordById($taskId)
    {
        if (!isset($this->_taskRecordsById[$taskId]))
        {
            $this->_taskRecordsById[$taskId] = TaskRecord::model()->findById($taskId);

            if (!$this->_taskRecordsById[$taskId])
            {
                $this->_taskRecordsById[$taskId] = false;
            }
        }

        if ($this->_taskRecordsById[$taskId])
        {
            return $this->_taskRecordsById[$taskId];
        }
    }
}
