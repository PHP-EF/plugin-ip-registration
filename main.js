function ipRegistrationGenerateAPIKey() {
    generateSecureToken().then(function(token) {
        $("[name=ApiToken]").val(token).change();
    });
}