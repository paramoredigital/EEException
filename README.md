# EEException

## Introduction
EEException is a handy add-on for [ExpressionEngine](http://expressionengine.com) that allows template developers (via a plugin) and add-on developers (via an extension hook) to report exceptions to [Codebase](http://codebasehq.com) where they can be more appropriately tracked.

In the context of our agency, EEException allows us to monitor issues that arise from all our web sites from a single dashboard, Codebase.

### Note: EEException requires PHP 5.3
If you don't have PHP 5.3, get with the times dude. It was released in 2009.

## Usage

### Installation
Follow these simple instructions to install EEException:

1. Copy the __eeexception__ add-on files to your __system/expressionengine/third_party__ directory.
2. Add the following line to your __config.php__ file: `$config['codebase_exceptions_api_key'] = 'xxxxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxx';`
3. Use the EEException plugin tag or call the hook method from your add-on code.

If there are any problems, EEException will log the error message in the developer log, which you can see when logged in to the EE control panel as a super admin.

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

## Coming Soon
These features are planned for the near future and will be implemented as time permits.

- Automatic logging of all PHP errors.
- Ability to log exception objects directly.
- Ability to edit API key from control panel.

## Contributing
Feel free to submit pull requests for new functionality. We'll review them for quality and merge them in if everything checks out.


