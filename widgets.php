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
        $WidgetConfig = $this->phpef->config->get('Widgets','IP Registration') ?? [];
        $PluginConfig = $this->phpef->config->get('Plugins','IP-Registration') ?? [];
        $Auth = $WidgetConfig['auth'] ?? 'IP-AUTH';
        $Enabled = $WidgetConfig['enabled'] ?? false;
        if ($this->phpef->auth->checkAccess($Auth) !== false && $Enabled) {
            $PlexDomain = $PluginConfig['PlexDomain'] ?? 'https://plex.tv';
            $PlexPort = $PluginConfig['PlexPort'] ?? '32400';
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
                        <div class="card card-rounded bg-inverse mb-lg-0 mb-2 monitorr-card">
                            <div class="card-body pt-1 pb-1">
                                <div class="d-flex no-block align-items-center">
                                    <div class="left-health bg-info" id="Info-Health"></div>
                                    <div class="ms-1 w-100 d-flex">
                                        <i class="float-right mt-2 mb-2 me-2 fa fa-check-circle h3 text-success" id="Info-Circle"></i>
                                        <h4 class="d-flex no-block align-items-center mt-2 mb-2" id="Info-Detail">Checking..</h4>
                                        <div class="clearfix"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 col-xs-12">
                        <div class="card card-rounded bg-inverse mb-lg-0 mb-2 monitorr-card">
                            <div class="card-body pt-1 pb-1">
                                <div class="d-flex no-block align-items-center">
                                    <div class="left-health bg-info" id="Connection-Health"></div>
                                    <div class="ms-1 w-100 d-flex">
                                        <i class="float-right mt-2 mb-2 me-2 fa fa-check-circle h3 text-info" id="Connection-Circle"></i>
                                        <h4 class="d-flex no-block align-items-center mt-2 mb-2" id="Connection-Detail">Checking..</h4>
                                        <div class="clearfix"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 col-xs-12">
                        <div class="card card-rounded bg-inverse monitorr-card">
                            <div class="card-body pt-1 pb-1">
                                <div class="d-flex no-block align-items-center">
                                    <div class="left-health bg-info" id="IP-Health"></div>
                                    <div class="ms-1 w-100 d-flex">
                                        <i class="float-right mt-2 mb-2 me-2 fa fa-check-circle h3 text-success" id="IP-Circle"></i>
                                        <h4 class="d-flex no-block align-items-center mt-2 mb-2" id="IP-Detail">Checking..</h4>
                                        <div class="clearfix"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <script>
            var ipDetail = $("#IP-Detail");
            var ipCircle = $("#IP-Circle");
            var ipHealth = $("#IP-Health");
            var infoDetail = $("#Info-Detail");
            var infoCircle = $("#Info-Circle");
            var infoHealth = $("#Info-Health");
            var connectionDetail = $("#Connection-Detail");
            var connectionCircle = $("#Connection-Circle");
            var connectionHealth = $("#Connection-Health");

            queryAPI('GET','/api/plugin/ipregistration/register').done(function(data) {
                if ($.inArray(data.data.Status, ['Error', 'Adding', 'Added', 'OK', 'Exists']) >= 0) {
                    ipCircle.removeClass("spinner-border text-light");
                    ipHealth.removeClass("bg-light");
                    infoCircle.removeClass("spinner-border text-light");
                    infoHealth.removeClass("bg-light");
                }
                if ($.inArray(data.data.Status, ['Error']) >= 0) {
                    ipCircle.addClass("text-danger fa fa-times-circle");
                    ipHealth.addClass("bg-danger");
                    infoCircle.addClass("text-danger fa fa-times-circle");
                    infoHealth.addClass("bg-danger");
                }
                if (data.data.Status == "Added") {
                    ipCircle.addClass("text-info fa fa-check-circle");
                    ipHealth.addClass("bg-info");
                    infoCircle.addClass("text-info fa fa-check-circle");
                    infoHealth.addClass("bg-info");
                }
                if ($.inArray(data.data.Status, ['Exists', 'OK']) >= 0) {
                    ipCircle.addClass("text-success fa fa-check-circle");
                    ipHealth.addClass("bg-success");
                    infoCircle.addClass("text-success fa fa-check-circle");
                    infoHealth.addClass("bg-success");
                }
                ipDetail.text(data.data.IP);
                infoDetail.text(data.data.Message);
            });

            function checkServer() {
                const p = new Ping();
                const server = "$PlexDomain"; // Try to get it automagically, but you can manually specify this
                const timeout = 3000; // Milliseconds

                p.ping(`$PlexDomain:$PlexPort`, (data) => {
                    if (data < timeout) {
                        connectionDetail.text("Plex is reachable.");
                        connectionCircle.removeClass("spinner-border text-light text-info text-danger");
                        connectionCircle.addClass("fa fa-check-circle text-success");
                        connectionHealth.removeClass("bg-light bg-danger");
                        connectionHealth.addClass("bg-success");
                    } else {
                        connectionDetail.text("Plex is unavailable.");
                        connectionCircle.removeClass("spinner-border text-light text-info text-success bg-success bg-light");
                        connectionCircle.addClass("fa fa-times-circle text-danger");
                        connectionHealth.addClass("bg-danger");
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