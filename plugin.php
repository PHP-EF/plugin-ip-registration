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

class ipRegistrationPlugin extends phpef {
	private $sql;
	private $sqlHelper;
	public $pluginConfig;

	public function __construct() {
		parent::__construct();
        $dbFile = dirname(__DIR__,2). DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'IP-Registration.db';
        $this->sql = new PDO("sqlite:$dbFile");
        $this->sql->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->sqlHelper = new dbHelper($this->sql);
		$this->pluginConfig = $this->config->get('Plugins','IP-Registration');
		$this->initialize();
	}

	private function initialize() {
		$this->hasDB();
	}

	public function _pluginGetSettings() {
		$TableAttributes = [
			'data-field' => 'data',
			'toggle' => 'table',
			'search' => 'true',
			'filter-control' => 'true',
			'show-refresh' => 'true',
			'pagination' => 'true',
			'toolbar' => '#toolbar',
			'show-columns' => 'true',
			'page-size' => '25',
			'response-handler' => 'responseHandler',
		];
	
		$IPTableColumns = [
			[
				'field' => 'id',
				'title' => 'Id',
				'dataAttributes' => ['visible' => 'false']
			],
			[
				'field' => 'type',
				'title' => 'Type',
                'dataAttributes' => ['sortable' => 'true'],
			],
			[
				'field' => 'ip',
				'title' => 'IP Address',
                'dataAttributes' => ['sortable' => 'true'],
			],
			[
				'field' => 'username',
				'title' => 'Username',
                'dataAttributes' => ['sortable' => 'true'],
			],
			[
				'field' => 'datetime',
				'title' => 'Date / Time',
                'dataAttributes' => ['sortable' => 'true'],
			],
			[
				'title' => 'Actions',
				'dataAttributes' => ['events' => 'ipRegistrationTableActionEvents', 'formatter' => 'deleteActionFormatter'],
			]
		];

		$IPTableAttributes = $TableAttributes;
		$IPTableAttributes['url'] = '/api/plugin/ipregistration/query';
		$IPTableAttributes['search'] = 'true';
		$IPTableAttributes['filter-control'] = 'true';
		$IPTableAttributes['show-refresh'] = 'true';
		$IPTableAttributes['pagination'] = 'true';
		$IPTableAttributes['toolbar'] = '#toolbar';
		$IPTableAttributes['sort-name'] = 'datetime';
		$IPTableAttributes['sort-order'] = 'asc';
		$IPTableAttributes['show-columns'] = 'true';
		$IPTableAttributes['page-size'] = '25';

		return array(
			'Plugin Settings' => array(
				$this->settingsOption('js', 'pluginJs', ['src' => '/api/page/plugin/IP-Registration/js']),
				$this->settingsOption('auth', 'Auth', ['label' => 'Restrict registration to a particular role']),
				$this->settingsOption('input', 'PfSense-IP', ['label' => 'The IP / FQDN of your pfsense appliance(s). Comma separated']),
				$this->settingsOption('input', 'PfSense-Username', ['label' => 'The username of your pfsense account']),
				$this->settingsOption('password', 'PfSense-Password', ['label' => 'The password of your pfsense account']),
				$this->settingsOption('input', 'PfSense-IPTable', ['label' => 'The name of the IP Alias in pfsense']),
				$this->settingsOption('input', 'PfSense-Maximum-IPs', ['label' => 'The maximum number of IP Addresses to retain in the database.', 'placeholder' => '100']),
				$this->settingsOption('passwordalt', 'ApiToken',['label' => 'IP Registration API Token']),
				$this->settingsOption('url', 'PlexDomain', ['label' => 'The domain for Plex to run availability checks against', 'placeholder' => 'https://myplexserver.site']),
				$this->settingsOption('input', 'PlexPort', ['label' => 'The port for Plex to run availability checks against.', 'placeholder' => '32400']),
				$this->settingsOption('button', '', ['label' => 'Generate API Token', 'icon' => 'fa fa-undo', 'text' => 'Retrieve', 'attr' => 'onclick="ipRegistrationGenerateAPIKey();"']),
			),
			'IP Addresses' => array(
				$this->settingsOption('bootstrap-table', 'IPTable', ['id' => 'IPTable', 'columns' => $IPTableColumns, 'dataAttributes' => $IPTableAttributes, 'width' => '12']),
			)
		);
	}

	private function hasDB() {
        if ($this->sql) {
            try {
                // Query to check if both tables exist
                $result = $this->sql->query("SELECT name FROM sqlite_master WHERE type='table' AND name IN ('ips')");
                $tables = $result->fetchAll(PDO::FETCH_COLUMN);

                if (in_array('ips', $tables)) {
                    return true;
                } else {
                    $this->createIPRegistrationTable();
                }
            } catch (PDOException $e) {
                $this->api->setAPIResponse("Error",$e->getMessage());
                return false;
            }
        } else {
            $this->api->setAPIResponse("Error","Database Not Initialized");
            return false;
        }
    }

    // Create IP Registration Table
    private function createIPRegistrationTable() {
        $this->sql->exec("CREATE TABLE IF NOT EXISTS ips (
            id INTEGER PRIMARY KEY AUTOINCREMENT UNIQUE,
            datetime TEXT,
            type TEXT,
            ip INTEGER,
            username TEXT
        )");
    }

	public function getIPRegistrations($UserIP = null, $Username = null, $viaAPIToken = false) {
		$auth = $this->auth->getAuth();
		if ($viaAPIToken || (isset($auth['isAdmin']) && $auth['isAdmin'] == true)) {
			if ($UserIP) {
				$dbquery = $this->sql->prepare('SELECT * FROM ips WHERE ip = :ip');
				$dbquery->execute([':ip' => $UserIP]);
			} elseif ($Username) {
				$dbquery = $this->sql->prepare('SELECT * FROM ips WHERE username = :username');
				$dbquery->execute([':username' => $Username]);
			} else {
				$dbquery = $this->sql->prepare('SELECT * FROM ips');
				$dbquery->execute();
			}
		} else {
			if ($auth['Authenticated']) {
				$dbquery = $this->sql->prepare('SELECT * FROM ips WHERE username = :username');
				$dbquery->execute([':username' => $auth['Username']]);
			}
		}
		return $dbquery->fetchAll(PDO::FETCH_ASSOC);
	}

	public function getOwnIPRegistrations($UserIP = null, $Username = null, $viaAPIToken = false) {
		$auth = $this->auth->getAuth();
		if ($auth['Authenticated']) {
			$dbquery = $this->sql->prepare('SELECT * FROM ips WHERE username = :username');
			$dbquery->execute([':username' => $auth['Username']]);
		}
			return $dbquery->fetchAll(PDO::FETCH_ASSOC);
	}

	private function getIPRegistrationById($id) {
		$dbquery = $this->sql->prepare('SELECT * FROM ips WHERE id = :id');
		$dbquery->execute([':id' => $id]);
		return $dbquery->fetch(PDO::FETCH_ASSOC);
	}

	public function getIPRegistrationList() {
		$IPs = $this->getIPRegistrations(null,null,true);
		$ipList = '';
		foreach ($IPs as $IP) {
			$ipList .= $IP['ip'] . PHP_EOL;
		}
		return $ipList;
	}

	private function newIPRegistration($datetime,$type,$ip,$username) {
		$dbquery = $this->sql->prepare('INSERT INTO ips (datetime,type,ip,username) VALUES (:datetime,:type,:ip,:username)');
		if ($dbquery->execute([':datetime' => $datetime, ':type' => $type, ':ip' => $ip, ':username' => $username])) {
			return true;
		} else {
			return false;
		}
	}

	public function deleteIPRegistration($id) {
		$registration = $this->getIPRegistrationById($id);
		if ($registration) {
			$dbquery = $this->sql->prepare('DELETE FROM ips WHERE id = :id');
			if ($dbquery->execute([':id' => $id])) {
				$this->logging->writeLog('IPRegistration','Successfully deleted IP Address from database','info',$registration);
				$this->api->setAPIResponseMessage('Successfully deleted IP Address from database.');
				return true;
			} else {
				$this->logging->writeLog('IPRegistration','Failed to delete IP Address from database','error',$registration);
				$this->api->setAPIResponse('Error','Failed to delete IP Address from database.',409,$registration);
				return false;
			}
		} else {
			$this->logging->writeLog('IPRegistration','IP Address does not exist in database','error',["id" => $id]);
			$this->api->setAPIResponse('Error','IP Address does not exist in database',409,["id" => $id]);
			return false;
		}
	}

	public function registerIP() {
		$User = $this->auth->getAuth();
		$Result = array (
			"Response" => array (
				"IP" => $User['IPAddress'],
				"Username" => $User['Username'],
				"Location" => "",
				"Status" => "",
				"Message" => ""
			)
		);
		if (filter_var($User['IPAddress'], FILTER_VALIDATE_IP)) {
			if (filter_var($User['IPAddress'], FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
		    	$DBResult = $this->getIPRegistrations($User['IPAddress']);
		        if ($DBResult) {
					$Result['Response']['Location'] = "External";
					$Result['Response']['Status'] = "Exists";
					$Result['Response']['Message'] = 'IP is already registered.';
					$this->logging->writeLog('IPRegistration','IP Address already registered in the database','debug',$Result);
					$this->api->setAPIResponse('Success','IP Address already registered in the database.',200,$Result['Response']);
					return $Result;
				} else {
					// Write to DB
					$IPRegistration = [
						'datetime' => date("j F Y g:ia"),
						'type' => 'Auto',
						'ip' => $User['IPAddress'],
						'username' => $User['Username']
					];
					if (!$this->newIPRegistration($IPRegistration['datetime'],$IPRegistration['type'],$IPRegistration['ip'],$IPRegistration['username'])) {
						$this->logging->writeLog('IPRegistration','Failed to add IP to database','error',$IPRegistration);
						$this->api->setAPIResponse('Error','Failed to add IP Address to database: '.$User['IPAddress'],409,$Result['Response']);
					} else {
						// Check it was added to Database OK
						$IPs = $this->getIPRegistrations($User['IPAddress']);
						if ($IPs) {
							$Result['Response']['Location'] = "External";
							$Result['Response']['Status'] = "Added";
							$Result['Response']['Message'] = "IP Address Registered Successfully.";
							$this->logging->writeLog('IPRegistration','Added IP Address to the database successfully','info',$Result);
							$this->api->setAPIResponse('Success','IP Address added to the database successfully: '.$User['IPAddress'],200,$Result['Response']);
							$this->updateFirewall();
							$this->reconcileDB();
							return $Result;
						} else {
							$Result['Response']['Location'] = "External";
							$Result['Response']['Status'] = "Error";
							$Result['Response']['Message'] = "Failed to add IP Address to database";
							$this->logging->writeLog('IPRegistration','Failed to add IP Address to the database','error',$Result);
							$this->api->setAPIResponse('Error','Failed to add IP Address to database.',409,$Result['Response']);
							return $Result;
						}
					}
				}
			} else {
				$Result['Response']['Status'] = "OK";
				$Result['Response']['Location'] = "Internal";
				$Result['Response']['Message'] = "Internal IP Address";
				$this->api->setAPIResponse('Success','Internal IP Address Found: '.$User['IPAddress'],200,$Result['Response']);
				$this->logging->writeLog('IPRegistration','Internal IP Address Found: '.$User['IPAddress'],'debug',$Result);
				return $Result;
			}
		} else {
			$Result['Response']['Status'] = "Bad IP Address";
			$Result['Response']['Location'] = "Internal";
			$Result['Response']['Message'] = "Internal IP Address";
			$this->api->setAPIResponse('Success','Bad IP Address Found: '.$User['IPAddress'],409,$Result['Response']);
			$this->logging->writeLog('IPRegistration','Bad IP Address Found: '.$User['IPAddress'],'error',$Result);
			return $Result;
		}
	}

	public function reconcileDB() {
		$MaxIPs = $this->pluginConfig['PfSense-Maximum-IPs'] ?? 100;
		if ($this->sql->query('DELETE FROM ips WHERE id NOT IN (SELECT id FROM ips ORDER BY id DESC LIMIT '.$MaxIPs.')')) {
			$this->logging->writeLog('IPRegistration','Database reconciliation successful','debug');
		} else {
			$this->logging->writeLog('IPRegistration','Database reconciliation failed','error');
		}
	}

	public function updateFirewall() {
		$PfSenseHosts = explode(',',$this->pluginConfig['PfSense-IP']);
		$PfSenseTable = $this->pluginConfig['PfSense-IPTable'] ?? null;
		if (!empty($PfSenseHosts) && $PfSenseTable) {
			require 'vendor/autoload.php';
			foreach($PfSenseHosts as &$PfSenseHost){
				$ssh = new phpseclib3\Net\SSH2($PfSenseHost);
				if (!$ssh->login($this->pluginConfig['PfSense-Username'], decrypt($this->pluginConfig['PfSense-Password'],$this->config->get('Security','Salt')))) {
					$this->logging->writeLog('IPRegistration','SSH Login Failed for '.$this->pluginConfig['PfSense-Username'].' on '.$PfSenseHost,'error');
					$this->api->setAPIResponse('Error', 'IP Registration Plugin: SSH Login Failed');
					$ssherror = true;
				} else {
					$result = $ssh->exec('sudo /etc/rc.update_urltables now forceupdate '.$PfSenseTable);
					if (!$result) {
						$this->logging->writeLog('IPRegistration','pfsense IP table refreshed successfully on '.$PfSenseHost,'debug',[$PfSenseTable.' on '.$PfSenseHost]);
						$this->api->setAPIResponseMessage('IP Registration Plugin: pfsense IP table '.$PfSenseTable.' refreshed successfully.');
						$ssherror = false;
					} else {
						$this->logging->writeLog('IPRegistration','Failed to refresh pfsense IP table '.$PfSenseTable.' on '.$PfSenseHost,'error',$result);
						$this->api->setAPIResponse('Error', 'IP Registration Plugin: Failed to refresh IP table '.$PfSenseTable);
						$ssherror = false;
					}
				}
				$ssh->disconnect();
			}
			if (!$ssherror) {
				return true;
			} else {
				return false;
			}
		} else {
			$this->logging->writeLog('IPRegistration','PfSense IP Address(es) not set','error');
		}
	}
}