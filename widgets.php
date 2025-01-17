<?php
// Define Custom HTML Widgets
class IPRegistrationWidget implements WidgetInterface {
    private $phpef;

    public function __construct($phpef) {
        $this->phpef = $phpef;
    }

    public function settings() {
        $customHTMLQty = 5;
        $SettingsArr = [];
        $SettingsArr['info'] = [
            'name' => 'IP Registration',
            'description' => 'Enables the IP Registration Widget',
			'image' => ''
        ];
        $SettingsArr['Settings'] = [
            "Widget Settings" => [
                $this->phpef->settingsOption('enable', 'enabled'),
				$this->phpef->settingsOption('auth', 'auth', ['label' => 'Role Required'])
            ]
        ];
        return $SettingsArr;
    }

    public function render() {
        $Config = $this->phpef->config->get('Widgets','IP Registration') ?? [];
        $Auth = $Config['auth'] ?? null;
        $Enabled = $Config['enabled'] ?? false;
        if ($this->phpef->auth->checkAccess($Auth) !== false && $Enabled) {
            $PlexDomain = $ipRegistrationPlugin->pluginConfig['PlexDomain'] ?? 'plex.tv';
            $PlexPort = $ipRegistrationPlugin->pluginConfig['PlexPort'] ?? '32400';
            return <<<EOF
            <style>
                .card-body {
                    margin-bottom: 0%!important;
                }
                .IP-cards {
                    display: block;
                }
            </style>

            <div class="col-md-12 homepage-item-collapse" data-bs-toggle="collapse" href="#ip-collapse" data-bs-parent="#ip" aria-expanded="true" aria-controls="ip-collapse">
                <h4 class="float-left homepage-item-title"><span lang="en">IP Registration</span></h4>
                <h4 class="float-left">&nbsp;</h4>
                <hr class="hr-alt ml-2">
            </div>
            <div class="panel-collapse collapse show" id="ip-collapse" aria-labelledby="ip-heading" role="tabpanel" aria-expanded="true" style="">
                <div class="row">
                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 col-xs-12">
                        <div class="card bg-inverse mb-3 monitorr-card">
                            <div class="card-body pt-1 pb-2">
                                <div class="d-flex no-block align-items-center">
                                    <div class="left-health bg-success" id="Info-Health"></div>
                                    <div class="ms-1 w-100 d-flex">
                                        <i class="float-right mt-2 mb-2 me-2 text-success fa fa-check-circle h3" id="Info-Circle"></i>
                                        <h4 class="d-flex no-block align-items-center mt-2 mb-2" id="Info">Internal IP Address</h4>
                                        <div class="clearfix"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 col-xs-12">
                        <div class="card bg-inverse mb-3 monitorr-card">
                            <div class="card-body pt-1 pb-1">
                                <div class="d-flex no-block align-items-center">
                                    <div class="left-health bg-success" id="Connection-Health"></div>
                                    <div class="ms-1 w-100 d-flex">
                                        <i class="float-right mt-2 mb-2 me-2 text-success fa fa-check-circle h3" id="Connection-Circle"></i>
                                        <h4 class="d-flex no-block align-items-center mt-2 mb-2" id="Connection">Plex is reachable.</h4>
                                        <div class="clearfix"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 col-xs-12">
                        <div class="card bg-inverse mb-3 monitorr-card">
                            <div class="card-body pt-1 pb-1">
                                <div class="d-flex no-block align-items-center">
                                    <div class="left-health bg-success" id="IP-Health"></div>
                                    <div class="ms-1 w-100 d-flex">
                                        <i class="float-right mt-2 mb-2 me-2 text-success fa fa-check-circle h3" id="IP-Circle"></i>
                                        <h4 class="d-flex no-block align-items-center mt-2 mb-2" id="IP">10.10.140.110</h4>
                                        <div class="clearfix"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <script>
            var xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                var RequestJSON = JSON.parse(this.responseText);
                document.getElementById("IP").innerHTML = RequestJSON.data.IP;
                document.getElementById("Info").innerHTML = RequestJSON.data.Message;
                
                if ($.inArray(RequestJSON.data.Status, ['Error', 'Adding', 'Added', 'OK', 'Exists']) >= 0) {
                    document.getElementById("IP-Circle").classList.remove("spinner-border","text-light");
                    document.getElementById("IP-Health").classList.remove("bg-light");
                    document.getElementById("Info-Circle").classList.remove("spinner-border","text-light");
                    document.getElementById("Info-Health").classList.remove("bg-light");
                }
                if ($.inArray(RequestJSON.data.Status, ['Error']) >= 0) {
                    document.getElementById("IP-Circle").classList.add("text-danger","fa","fa-times-circle");
                    document.getElementById("IP-Health").classList.add("bg-danger");
                    document.getElementById("Info-Circle").classList.add("text-danger","fa","fa-times-circle");
                    document.getElementById("Info-Health").classList.add("bg-danger");
                }
                if (RequestJSON.data.Status == "Added") {
                    document.getElementById("IP-Circle").classList.add("text-info","fa","fa-check-circle");
                    document.getElementById("IP-Health").classList.add("bg-info");
                    document.getElementById("Info-Circle").classList.add("text-info","fa","fa-check-circle");
                    document.getElementById("Info-Health").classList.add("bg-info");
                }
                if ($.inArray(RequestJSON.data.Status, ['Exists', 'OK']) >= 0) {
                    document.getElementById("IP-Circle").classList.add("text-success","fa","fa-check-circle");
                    document.getElementById("IP-Health").classList.add("bg-success");
                    document.getElementById("Info-Circle").classList.add("text-success","fa","fa-check-circle");
                    document.getElementById("Info-Health").classList.add("bg-success");
                }
            }
            };
            xmlhttp.open("GET", "/api/plugin/ipregistration/register", true);
            xmlhttp.send();
            </script>

            <script type="text/javascript">
                function checkServer() {
                    const p = new Ping();
                    const server = "$PlexDomain"; // Try to get it automagically, but you can manually specify this
                    const timeout = 3000; // Milliseconds

                    p.ping(`$PlexDomain:$PlexPort`, (data) => {
                        const serverMsg = document.getElementById("Connection");
                        const connectionCircle = document.getElementById("Connection-Circle");
                        const connectionHealth = document.getElementById("Connection-Health");

                        if (data < timeout) {
                            serverMsg.innerHTML = "Plex is reachable.";
                            connectionCircle.classList.remove("spinner-border", "text-light");
                            connectionHealth.classList.remove("bg-light", "bg-danger");
                            connectionCircle.classList.add("fa", "fa-check-circle", "text-success");
                            connectionHealth.classList.add("bg-success");
                        } else {
                            serverMsg.innerHTML = "Plex is unavailable.";
                            connectionCircle.classList.remove("spinner-border", "text-light");
                            connectionHealth.classList.remove("bg-light", "bg-success");
                            connectionCircle.classList.add("fa", "fa-times-circle", "text-danger");
                            connectionHealth.classList.add("bg-danger");
                            setTimeout(checkServer, 5000);
                        }
                    }, timeout);
                }
                checkServer();
            </script>
            EOF;
        }
    }
}

// Register Custom HTML Widgets
$phpef->dashboard->registerWidget('IP Registration', new IPRegistrationWidget($phpef));