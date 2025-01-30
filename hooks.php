<?php
// Add hook to include custom JS to login page
$this->addHook('user_profile_body', function() {
    global $phpef;
    // Check if plugin is enabled
    $allowed = $phpef->auth->checkAccess($phpef->config->get("Plugins", "IP-Registration")['Auth'] ?? "IP-AUTH");

    if ($allowed) {
        echo '
        <hr>
        <div class="row">
            <div class="accordion" id="ipRegistrationAccordion">
              <div class="accordion-item">
                <h2 class="accordion-header" id="ipRegistrationHeading">
                  <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#ipRegistration" aria-expanded="true" aria-controls="ipRegistration">
                  Registered IP Addresses
                  </button>
                </h2>
                <div id="ipRegistration" class="accordion-collapse collapse" aria-labelledby="ipRegistrationHeading" data-bs-parent="#ipRegistrationAccordion">
                    <script>
                    window.ipRegistrationTableActionEvents = {
                        "click .delete": function (e, value, row, index) {
                        if(confirm("Are you sure you want to delete the IP Address: "+row.ip+"? This is irriversible.") == true) {
                            queryAPI("DELETE","/api/plugin/ipregistration/ip/"+row.id).done(function(data) {
                            if (data["result"] == "Success") {
                                toast("Success","","Successfully deleted "+row.ip+" from IP Addresses","success");
                                var tableId = `#${$(e.currentTarget).closest("table").attr("id")}`;
                                $(tableId).bootstrapTable("refresh");
                            } else if (data["result"] == "Error") {
                                toast(data["result"],"",data["message"],"danger","30000");
                            } else {
                                toast("Error","","Failed to delete "+row.ip+" from IP Addresses","danger");
                            }
                            }).fail(function() {
                                toast("Error", "", "Failed to remove " + row.ip + " from IP Addresses", "danger");
                            });
                        }
                        }
                    }
                    </script>
                    <table  data-url="/api/plugin/ipregistration/own"
                        data-data-field="data"  
                        data-toggle="table"
                        data-search="true"
                        data-filter-control="true"
                        data-show-filter-control-switch="true"
                        data-filter-control-visible="false"
                        data-filter-control-multiple-search="true"
                        data-show-export="true"
                        data-export-data-type="json, xml, csv, txt, excel, sql"
                        data-show-refresh="true"
                        data-show-columns="true"
                        data-pagination="true"
                        data-toolbar="#toolbar"
                        class="table table-striped"
                        id="ipRegistrationTable">

                        <div id="toolbar" class="select">
                        </div>
                        <thead>
                        <tr>
                            <th data-field="ip" data-sortable="true" data-filter-control="input">IP Address</th>
                            <th data-field="datetime" data-sortable="true" data-filter-control="input">Date/Time</th>
                            <th data-formatter="deleteActionFormatter" data-events="ipRegistrationTableActionEvents"></th>
                        </tr>
                        </thead>
                    </table>
                </div>
              </div>
            </div>
          </div>
        ';
    }
});