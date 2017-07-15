# taskscli

## Run your taks through the cli batching steps in childs processes

Craft CMS's tasks, depenging on what has to be accomplished, can be very tough on memory.
For exemple, if you try to get and update hundred of entries, you may run out of memory.

This plugin breaks a task's steps into configurable batches and run these batches in a child process.

### Installation

Download and unzip in Craft's plugin folder, be sure to rename the folder to `taskscli`

### Configuration

By default, the plugin will aim to make 25 steps per batch. To configure this setting, copy the config.php file in the repo, paste it in your craft/config folder and rename it `taskscli.php`

Make sure that you have the `runTasksAutomatically` setted to false in your config.

### Running

You can run the tasks by calling the `runTasks` command through Craft 

`$ php craft/app/etc/console/yiic taskscli runTasks`


Ideally, You should setup a cron to do so.

`*/1 * * * /usr/bin/php craft/app/etc/console/yiic taskscli runTasks >> craft/storage/runtime/logs/taskscli.log 2>&1`
 
