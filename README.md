# EEException

## Introduction
EEException is a handy add-on for [ExpressionEngine](http://expressionengine.com) that allows template developers (via a plugin) and add-on developers (via an extension hook) to report exceptions to [Codebase](http://codebasehq.com) where they can be more appropriately tracked.

In the context of our agency, EEException allows us to monitor issues that arise from all our web sites from a single dashboard, Codebase.

### Note: EEException requires PHP 5.3
If you don't have PHP 5.3, get with the times dude. It was released in 2009.

## Installation
Follow these simple instructions to install EEException:

1. Copy the __eeexception__ add-on files to your __system/expressionengine/third_party__ directory.
2. Edit your config.php file per the instructions below.
3. Use the EEException plugin tag or call the hook method from your add-on code.

If there are any problems, EEException will log the error message in the developer log, which you can see when logged in to the EE control panel as a super admin.

### Config File
EEException can report exceptions to just about any service. In order for EEException to know how to report exceptions, you must set some configuration values in your `system/expressionengine/config/config.php` file. This is the minimum required configuration for the Airbrake notifier (for Codebase or airbrake.io):

	$config['eeexception_config'] = array(
		'default_notifier' => 'airbrake',
		'notifier_config' => array(
			'airbrake' => array(
				'apiEndPoint' => 'https://exceptions.codebasehq.com/notifier_api/v2/notices',
				'apiKey' => '1241ad63-8cb6-eaa8-7fc0-f3f6802d2c53'
			)
		)
	);

For a list of all the possible configuration options. See the notifier documentation below.

## Usage

### In your add-ons
Reporting exceptions to Codebase is all the rage these days. To facilitate this, EEException exposes a special extension hook that you can use to report exceptions in sites that have EEException installed.

To send an exception to Codebase, call the `eeexception_send_string` hook.

	if (TRUE === $this->EE->extensions->active_hook('eeexception_send_string'))
		$this->EE->extensions->call('eeexception_send_string', $error_message);

Note: It's okay to distribute this call to the EEException hook with your application. If a user does not have EEException installed, your code will continue without any hitch.

### In your templates
Suppose you have a Channel Entries tag that pulls in a specific entry. You've done something like this:

	{exp:channel:entries
		entry_id="10"
		dynamic="no"
		limit="1"
	}
		{if no_results}
			Uh oh! This entry doesn't exist anymore!
		{/if}

		...

	{/exp:channel:entries}

In your `{if no_results}` conditional, you might just redirect to the 404 page. That is fine, but you still have no idea that some random page is broken because the client accidentally deleted the wrong entry (or changed it's status to closed). Using EEException, you can easily notify yourself of this problem. In your template simply insert the following code in your `{if no_results}` conditional tag pair.

	{if no_results}
		{exp:eeexception:notify 
			error_message="Unable to find Entry 10. The page is broken."
		}
	{/if}

## Extension Hooks

### eeexception_send_string
Sends the provided string to Codebase.

__Parameters__

`error_message` (string) the error message you want to log as an exception.

__Returns:__ nothing

## Plugin API

### {exp:eeexception:notify}
Sends an exception string to Codebase.

__Parameters__

`error_message` (string) the error message you want to log as an exception.

__Returns:__ A blank string so that it will not affect your template formatting.

## Notifier Configuration
Each EEException notifier can have several configuration parameters. You'll find the possible configuration parameters for the various built-in notifiers below.

### Airbrake Notifier

Supports sending exceptions to [Codebase](http://codebasehq.com/) and [Airbrake](http://airbrake.io).

`apiEndPoint`
__Required__ The API URL of the airbrake-enabled service.
	- Codebase: https://exceptions.codebasehq.com/notifier_api/v2/notices
	- Airbrake.io: http://api.airbrake.io/notifier_api/v2/notices

`apiKey`
__Required__ Your API key. For codebase, this is found under the __Exceptions__ tab in your project.

`environmentName`
*Optional* Defaults to "production"

`component`
*Optional* Allows you to specify what component the exception occurred in. You might use your plugin/module name here.

`action`
*Optional* Allows you to specify what action was happening when the exception occurred.

`url`
*Optional* EEException will use the current URL by default, but you may override this if you wish.

## Coming Soon
These features are planned for the near future and will be implemented as time permits.

- Automatic logging of all PHP errors.
- Ability to log exception objects directly.
- Ability to edit API key from control panel.

## Contributing
Feel free to submit pull requests for new functionality. We'll review them for quality and merge them in if everything checks out.


