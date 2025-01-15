<?php
// Get plugin settings
$app->get('/plugin/ipregistration/settings', function ($request, $response, $args) {
	$ipRegistrationPlugin = new ipRegistrationPlugin();
	if ($ipRegistrationPlugin->auth->checkAccess('ADMIN-CONFIG')) {
		$ipRegistrationPlugin->api->setAPIResponseData($ipRegistrationPlugin->_pluginGetSettings());
	}
	$response->getBody()->write(jsonE($GLOBALS['api']));
	return $response
		->withHeader('Content-Type', 'application/json;charset=UTF-8')
		->withStatus($GLOBALS['responseCode']);
});
// Admin function to force update of firewall ACL
$app->post('/plugin/ipregistration/update', function ($request, $response, $args) {
	$ipRegistrationPlugin = new ipRegistrationPlugin();
	if ($ipRegistrationPlugin->auth->checkAccess('ADMIN-CONFIG')) {
		$ipRegistrationPlugin->updateFirewall();
	}
	$response->getBody()->write(jsonE($GLOBALS['api']));
	return $response
		->withHeader('Content-Type', 'application/json;charset=UTF-8')
		->withStatus($GLOBALS['responseCode']);
});
// Register a new IP Address
$app->get('/plugin/ipregistration/register', function ($request, $response, $args) {
	$ipRegistrationPlugin = new ipRegistrationPlugin();
	if ($ipRegistrationPlugin->auth->checkAccess($ipRegistrationPlugin->config->get("Plugins", "IP-Registration")['auth'] ?? "IP-AUTH")) {
		$ipRegistrationPlugin->registerIP();
	}
	$response->getBody()->write(jsonE($GLOBALS['api']));
	return $response
		->withHeader('Content-Type', 'application/json;charset=UTF-8')
		->withStatus($GLOBALS['responseCode']);
});

$app->get('/plugin/ipregistration/query', function ($request, $response, $args) {
	$ipRegistrationPlugin = new ipRegistrationPlugin();
	if ($ipRegistrationPlugin->auth->checkAccess($ipRegistrationPlugin->config->get("Plugins", "IP-Registration")['auth'] ?? "IP-AUTH")) {
		$ipRegistrationPlugin->api->setAPIResponseData($ipRegistrationPlugin->_ipRegistrationPluginQueryIPs());
	}
	$response->getBody()->write(jsonE($GLOBALS['api']));
	return $response
		->withHeader('Content-Type', 'application/json;charset=UTF-8')
		->withStatus($GLOBALS['responseCode']);
});
$app->get('/plugin/ipregistration/list', function ($request, $response, $args) {
	$ipRegistrationPlugin = new ipRegistrationPlugin();
	if ($ipRegistrationPlugin->auth->checkAccess($ipRegistrationPlugin->config->get("Plugins", "IP-Registration")['auth'] ?? "IP-AUTH")) {
		$ipRegistrationPlugin->api->setAPIResponseData($ipRegistrationPlugin->getIPRegistrationList());
	}
	$response->getBody()->write(jsonE($GLOBALS['api']));
	return $response
		->withHeader('Content-Type', 'application/json;charset=UTF-8')
		->withStatus($GLOBALS['responseCode']);
});
$app->delete('/plugin/ipregistration/ip/{id}', function ($request, $response, $args) {
	$ipRegistrationPlugin = new ipRegistrationPlugin();
	if ($ipRegistrationPlugin->auth->checkAccess($ipRegistrationPlugin->config->get("Plugins", "IP-Registration")['auth'] ?? "IP-AUTH")) {
		$id = $args['id'] ?? null;
		$ipRegistrationPlugin->api->setAPIResponseData($ipRegistrationPlugin->_ipRegistrationPluginDeleteIP($id));
	}
	$response->getBody()->write(jsonE($GLOBALS['api']));
	return $response
		->withHeader('Content-Type', 'application/json;charset=UTF-8')
		->withStatus($GLOBALS['responseCode']);
});