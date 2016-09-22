# EEException

## Introduction
EEException is a handy add-on for [ExpressionEngine](http://expressionengine.com) that allows template developers (via a plugin) and add-on developers (via an extension hook) to report exceptions to [Airbrake](http://airbrake.io./) or [Codebase](http://codebasehq.com/). With a little elbow grease, any developer can extend EEException to log exceptions virually anywhere.

ExpressionEngine's developer log is nice, but we have a lot of ExpressionEngine-driven web sites and we needed a way to monitor problems with all of our sites as a whole. EEException allows us to monitor issues that may arise with any of our sites from a single dashboard in Codebase.

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
				'host' => 'https://exceptions.codebasehq.com',
				'apiKey' => 'xxxxxxx-xxxxxxx-xxxxxxx-xxxxxxxx',
				'projectKey' => 'xxxxxxx-xxxxxxx-xxxxxxx-xxxxxxxx',
                'projectId' => 'xxxxx',
                'rootDirectory' => $_SERVER['DOCUMENT_ROOT']
			)
		)
	);

For a list and explanation of all the possible configuration options. See the notifier documentation below.

## Usage

### In your add-ons
EEException exposes a special extension hook that add-on developers can use to report exceptions in sites that have EEException installed. This is useful for alerting your users of configuration problems, third-party integration failures, non-writeable cache directories, etc.

To send an exception to Codebase, call the `eeexception_send_string` hook.

	if (TRUE === $this->EE->extensions->active_hook('eeexception_send_string'))
		$this->EE->extensions->call('eeexception_send_string', $error_code, $error_message, $notifier_config_overrides);

See the Extension API documentation below for more information.


$error_code can have the following values:

 - E_NOTICE 
 - E_USER_NOTICE 
 - E_WARNING 
 - E_USER_WARNING 
 - E_ERROR 
 - E_CORE_ERROR 
 - E_RECOVERABLE_ERROR 
 - E_USER_ERROR 
 
 
__Note:__ It's okay to distribute this call to the EEException hook with your add-on. If a user does not have EEException installed, your code will continue without any hitch.

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
			error_code="E_ERROR" error_message="Unable to find Entry 10. The page is broken."
		}
	{/if}

## Extension Hooks

### eeexception_register_handler
Register an Airbrake Exception Handler

### eeexception_send_string
Sends the provided string to Codebase.

__Parameters__

- `$error_code` (int, required) The error level must have one of these values:

    - E_NOTICE 
    - E_USER_NOTICE 
    - E_WARNING 
    - E_USER_WARNING 
    - E_ERROR 
    - E_CORE_ERROR 
    - E_RECOVERABLE_ERROR 
    - E_USER_ERROR 

- `$error_message` (string, required) The exception message to log.

- `$notifier_config_overrides` (array, optional) An array of notifier configuration options to override. This can be useful for specifying additional environment information in notifiers that support such parameters. See the notifier configuration section below for more details.

__Returns:__ nothing

## Plugin API

### {exp:eeexception:register_handler}
Register an Exception Handler

### {exp:eeexception:notify}
Sends an exception string to Codebase.

__Parameters__
`error_code` (string, required) the error code. Must be one of these values:
                                               
    - E_NOTICE 
    - E_USER_NOTICE 
    - E_WARNING 
    - E_USER_WARNING 
    - E_ERROR 
    - E_CORE_ERROR 
    - E_RECOVERABLE_ERROR 
    - E_USER_ERROR 

`error_message` (string, required) the error message you want to log as an exception.

__Returns:__ A blank string so that it will not affect your template formatting.

## Notifier Configuration
Each EEException notifier can have several configuration parameters. You'll find the possible configuration parameters for the various built-in notifiers below.

### Airbrake Notifier

Supports sending exceptions to [Codebase](http://codebasehq.com/) and [Airbrake](http://airbrake.io).

`host`
(string, required) The API URL of the airbrake-enabled service.

- Codebase: https://exceptions.codebasehq.com
- Airbrake.io: http://api.airbrake.io

`apiKey`
(string, required) Your API key. For codebase, this is found under the __Exceptions__ tab in your project.

`projectKey`
(string, required) Your Project key. For codebase, this is found under the __Exceptions__ tab in your project.

`projectId`
(int, required) Your Project ID. For codebase, this is found under the __Exceptions__ tab in your project.

`environment`
(string, optional) Defaults to "production"

`url`
(string, optional) EEException will use the current URL by default, but you may override this if you wish.

## Coming Soon
These features are planned for the near future and will be implemented as time permits.

- Ability to log exception objects directly.
- Ability to edit API key from control panel.
- Ability to filter Exception to report when Handler is registered

## Contributing
Feel free to submit pull requests for new functionality. We'll review them for quality and merge them in if everything checks out.


