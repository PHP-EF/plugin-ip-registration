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
        $SettingsArr['Settings'] = [];
        return $SettingsArr;
    }

    public function render() {
        return <<<EOF
        <style>
            .card-body {
                margin-bottom: 0%!important;
            }
            .IP-cards {
                display: block;
            }
        </style>
        <div class="row">
            <div class="card">
                <div class="card-header">
                    <h4 class="pull-left homepage-element-title"><span lang="en">IP Registration</span></h4>
                </div>
                <div class="card-body">
            
                <div class="row">
                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 col-xs-12">
                        <div class="card bg-inverse text-white mb-3 monitorr-card">
                            <div class="card-body bg-org-alt pt-1 pb-1">
                                <div class="d-flex no-block align-items-center">
                                    <div class="left-health bg-success" id="Info-Health"></div>
                                    <div class="ml-1 w-100">
                                        <i class="font-20 pull-right mt-3 mb-2 text-success fa fa-check-circle" id="Info-Circle"></i>
                                        <h3 class="d-flex no-block align-items-center mt-2 mb-2" id="Info">Internal IP Address</h3>
                                        <div class="clearfix"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 col-xs-12">
                        <div class="card bg-inverse text-white mb-3 monitorr-card">
                            <div class="card-body bg-org-alt pt-1 pb-1">
                                <div class="d-flex no-block align-items-center">
                                    <div class="left-health bg-success" id="Connection-Health"></div>
                                    <div class="ml-1 w-100">
                                        <i class="font-20 pull-right mt-3 mb-2 fa fa-check-circle text-success" id="Connection-Circle"></i>
                                        <h3 class="d-flex no-block align-items-center mt-2 mb-2" id="Connection">Plex is reachable.</h3>
                                        <div class="clearfix"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 col-xs-12">
                        <div class="card bg-inverse text-white mb-3 monitorr-card">
                            <div class="card-body bg-org-alt pt-1 pb-1">
                                <div class="d-flex no-block align-items-center">
                                    <div class="left-health bg-success" id="IP-Health"></div>
                                        <div class="ml-1 w-100">
                                            <i class="font-20 pull-right mt-3 mb-2 text-success fa fa-check-circle" id="IP-Circle"></i>
                                            <h3 class="d-flex no-block align-items-center mt-2 mb-2" id="IP">10.10.140.110</h3>
                                            <div class="clearfix"></div>
                                        </div>
                                    </div>
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
            console.log(RequestJSON);
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

        <script>
        var Ping=function(){this._version="0.0.1"};Ping.prototype.ping=function(a,b,c){function d(){e&&clearTimeout(e);var a=new Date-f;"function"==typeof b&&b(a)}this.img=new Image,c=c||0;var e,f=new Date;this.img.onload=this.img.onerror=d,c&&(e=setTimeout(d,c)),this.img.src="//"+a+"/?"+ +new Date};
        </script>
        <script type="text/javascript">
            function checkServer() {
                var p = new Ping();
                var server = "app.tmmn.uk"; //Try to get it automagically, but you can manually specify this
                var timeout = 3000; //Milliseconds
                p.ping(server+":443", function(data) {
                    var serverMsg = document.getElementById( "Connection" );
                    if (data < 3000){
                        serverMsg.innerHTML = "Plex is reachable.";
                        document.getElementById("Connection-Circle").classList.remove("spinner-border","text-light");
                        document.getElementById("Connection-Health").classList.remove("bg-light","bg-danger");
                        document.getElementById("Connection-Circle").classList.add("fa","fa-check-circle","text-success");
                        document.getElementById("Connection-Health").classList.add("bg-success");
                    }else{
                        serverMsg.innerHTML = "Plex is unavailable.";
                        document.getElementById("Connection-Circle").classList.remove("spinner-border","text-light");
                        document.getElementById("Connection-Health").classList.remove("bg-light","bg-success");
                        document.getElementById("Connection-Circle").classList.add("fa","fa-times-circle","text-danger");
                        document.getElementById("Connection-Health").classList.add("bg-danger");
                        setTimeout("checkServer()",5000);
                    }
                }, timeout);
            }
            checkServer();
        </script>
        EOF;
    }
}

// Register Custom HTML Widgets
$phpef->dashboard->registerWidget('IP Registration', new IPRegistrationWidget($phpef));