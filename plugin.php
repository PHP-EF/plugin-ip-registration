<?php
// **
// USED TO DEFINE PLUGIN INFORMATION & CLASS
// **

// PLUGIN INFORMATION - This should match what is in plugin.json
$GLOBALS['plugins']['IP-Registration'] = [ // Plugin Name
	'name' => 'IP-Registration', // Plugin Name
	'author' => 'TehMuffinMoo', // Who wrote the plugin
	'category' => 'Access Management', // One to Two Word Description
	'link' => 'https://github.com/php-ef/plugin-ip-registration', // Link to plugin info
	'version' => '1.0.0', // SemVer of plugin
	'image' => 'logo.png', // 1:1 non transparent image for plugin
	'settings' => true, // does plugin need a settings modal?
	'api' => '/api/plugin/ipregistration/settings', // api route for settings page, or null if no settings page
];

class ipRegistrationPlugin extends phpef
{

	public function __construct() {
		parent::__construct();
	}

	public function _pluginGetSettings()
	{
		return array(
			'Plugin Settings' => array(
				$this->settingsOption('js', 'pluginJs', ['src' => '/api/page/plugin/IP-Registration/js']),
				$this->settingsOption('auth', 'Auth', ['label' => 'Restrict registration to a particular role']),
				$this->settingsOption('input', 'PfSense-IP', ['label' => 'The IP / FQDN of your pfsense']),
				$this->settingsOption('input', 'PfSense-Username', ['label' => 'The username of your pfsense account']),
				$this->settingsOption('password', 'PfSense-Password', ['label' => 'The password of your pfsense account']),
				$this->settingsOption('input', 'PfSense-IPTable', ['label' => 'The name of the IP Alias in pfsense']),
				$this->settingsOption('input', 'PfSense-Maximum-IPs', ['label' => 'The maximum number of IP Addresses to retain in the database.']),
				$this->settingsOption('passwordalt', 'ApiToken',['label' => 'IP Registration API Token']),
				$this->settingsOption('button', '', ['label' => 'Generate API Token', 'icon' => 'fa fa-undo', 'text' => 'Retrieve', 'attr' => 'onclick="ipRegistrationGenerateAPIKey();"']),
			),
		);
	}
}