function ipRegistrationGenerateAPIKey() {
    generateSecureToken().then(function(token) {
        $("[name=ApiToken]").val(token).change();
    });
}

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