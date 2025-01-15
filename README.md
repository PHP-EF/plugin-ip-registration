# RegisterIP
PHP-EF Plugin for IP Registration against PfSense. It allows restricting access to Plex or other services through using PfSense firewall(s) Alias IP Tables.

You create an alias within PfSense to be used against your preferred rules, the URL used in the Alias is provided by the plugin. Within PHP-EF, you specify an account (non-privileged accounts can be set up using the info below), the IP/FQDN and the Alias IP Table name to the plugin and it will maintain a list of registered IP addresses and force update the pfsense table on demand.

This can be used to restrict access to services at a firewall level until a user has successfully authenticated in PHP-EF. It is quick and seamless and will update the firewall in under 2 seconds from PHP-EF homepage launch.

| :exclamation: Important                                                          |
|:---------------------------------------------------------------------------|
| To add this plugin to PHP-EF, please add https://github.com/PHP-EF/plugin-ip-registration to the Plugins Marketplace within your PHP-EF instance. |


## How-to
### Configuring the plugin
Before configuring PfSense, you must generate a new API key in the plugin settings and save. Once you have created an Alias and optional privileged account within PfSense, you must return to the plugin settings to enter these here.

An administrator can view all registered IP addresses via the same top-right menu of Organizr.

<img src="https://user-images.githubusercontent.com/51195492/138614316-cdf7c842-9f67-4b6b-a93e-fb6ec097a6bc.gif" width="40%" height="40%"/>

### Configuring PfSense
There are only a couple of steps required in PfSense.

#### Create Alias
1) Create a new IP Alias via Firewall -> Aliases
2) Select "URL (IPs)" as the Type
3) For the URL, enter your PHP-EF URL followed by `api/plugin/ipregistration/list?ApiKey=PluginAPIKey`
    - I.e: `https://yourphpefurl.com/api/plugin/ipregistration/list?ApiKey=feFgGh4rt4twses`

#### Create Restricted Account (Recommended)
It is recommended to create a non-privilleged account to be used by the IP Registration plugin. You can do this by making use of the PfSense sudo package.
1) In PfSense, go to System -> Package Manager -> Available Packages and install sudo
2) Once installed, head to System -> User Manager to create a new account
3) Add a new account granting it `User - System: Shell account access` and saving
4) Next go to System -> Sudo
5) Add a new User Privilege, selecting your new privileged user.
6) Select "user: root" as the Run As account, and **check** the `No Password` checkbox to prevent re-prompting for sudo password.
7) In the Command List, enter `/etc/rc.update_urltables now forceupdate IP_Alias_Name` where IP_Alias_Name is the Alias you created earlier
8) Save and you're done