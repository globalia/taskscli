<?php
namespace Craft;


class TasksCliCommand extends BaseCommand {

    public function actionRunTasks() {

        // Make sure tasks aren't already running
        if ( ! craft()->tasksCli->isTaskRunning() ) {
            // Is there a pending task?
            $task = craft()->tasksCli->getNextPendingTask();

            if ( $task ) {
                // Start running tasks
                craft()->tasksCli->runPendingTasks();
            }
        }

        craft()->end();
    }


    public function actionRunTaskBatch($taskId) {

        craft()->config->maxPowerCaptain();
        $task     = craft()->tasks->getTaskById( $taskId );
        $taskType = $task->getTaskType();
        $step = $task->currentStep ? $task->currentStep : 0;

        $stepsPerBatch = craft()->config->get('stepsPerBatch', 'taskscli');
        $max = min($task->currentStep + $stepsPerBatch, $task->totalSteps);

        echo 'Current Step : '.$task->currentStep.PHP_EOL;
        echo 'Per batch : ' .$stepsPerBatch.PHP_EOL;
        echo 'Total : '.  $task->totalSteps.PHP_EOL;

        for($step; $step < $max; $step++ ){
            echo '.';
            $task->currentStep = $step+1;
            craft()->tasksCli->saveTask($task);
            $taskType->runStep( $task->currentStep );
        }
        echo PHP_EOL;
        $this->writeln(sprintf('Batch done (Memory: %s MB)', round(memory_get_usage() / 1024 / 1024, 2)));
        craft()->end();
    }

    private function writeln( $str ) {
        $now = new DateTime( 'now' );
        echo sprintf( '%s - %s', $now->mySqlDateTime(), $str ) . PHP_EOL;
    }

}