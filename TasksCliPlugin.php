<?php
namespace Craft;

class TasksCliPlugin extends BasePlugin
{
	function getName()
	{
		return Craft::t('Tasks thru CLI');
	}

	function getVersion()
	{
		return '1.0';
	}

	function getDeveloper()
	{
		return 'Globalia';
	}

	function getDeveloperUrl()
	{
		return 'http://www.globalia.ca';
	}

	public function init()
	{
        if (craft()->config->get('runTasksAutomatically')) {

            // First disable plugin
            // With this we force Craft to look up the plugin's ID, which isn't cached at this moment yet
            // Without this we get a fatal error
            // craft()->plugins->disablePlugin($this->getClassHandle());

            // Uninstall plugin
            // craft()->plugins->uninstallPlugin($this->getClassHandle());

            // Show error message
            //craft()->userSession->setError(Craft::t('The config setting "runTasksAutomatically" needs to be `false` for this plugin to work'));
        }
	}
    public function onAfterInstall()
    {

    }
}
