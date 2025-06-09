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
	if ($ipRegistrationPlugin->auth->checkAccess($ipRegistrationPlugin->config->get("Plugins", "IP-Registration")['Auth'] ?? "IP-AUTH")) {
		$ipRegistrationPlugin->registerIP();
	}
	$response->getBody()->write(jsonE($GLOBALS['api']));
	return $response
		->withHeader('Content-Type', 'application/json;charset=UTF-8')
		->withStatus($GLOBALS['responseCode']);
});

$app->get('/plugin/ipregistration/own', function ($request, $response, $args) {
	$ipRegistrationPlugin = new ipRegistrationPlugin();
	if ($ipRegistrationPlugin->auth->checkAccess($ipRegistrationPlugin->config->get("Plugins", "IP-Registration") ['Auth'] ?? "IP-AUTH")) {
		$ipRegistrationPlugin->api->setAPIResponseData($ipRegistrationPlugin->getOwnIPRegistrations());
	}
	$response->getBody()->write(jsonE($GLOBALS['api']));
	return $response
		->withHeader('Content-Type', 'application/json;charset=UTF-8')
		->withStatus($GLOBALS['responseCode']);
});

$app->get('/plugin/ipregistration/query', function ($request, $response, $args) {
	$ipRegistrationPlugin = new ipRegistrationPlugin();
	if ($ipRegistrationPlugin->auth->checkAccess("ADMIN-CONFIG")) {
		$ipRegistrationPlugin->api->setAPIResponseData($ipRegistrationPlugin->getIPRegistrations());
	}
	$response->getBody()->write(jsonE($GLOBALS['api']));
	return $response
		->withHeader('Content-Type', 'application/json;charset=UTF-8')
		->withStatus($GLOBALS['responseCode']);
});

$app->get('/plugin/ipregistration/list', function ($request, $response, $args) {
	$ipRegistrationPlugin = new ipRegistrationPlugin();
	$data = $request->getQueryParams();
	if (!empty($ipRegistrationPlugin->pluginConfig['ApiToken'] && $data['ApiKey'] == $ipRegistrationPlugin->pluginConfig['ApiToken']) || $ipRegistrationPlugin->auth->checkAccess('ADMIN-CONFIG')) {
		$ipRegistrationPlugin->api->setAPIResponseData($ipRegistrationPlugin->getIPRegistrationList());
		$response->getBody()->write($GLOBALS['api']['data']);
		return $response
			->withHeader('Content-Type', 'text/plain')
			->withStatus($GLOBALS['responseCode']);
	} else {
		$response->getBody()->write(jsonE($GLOBALS['api']));
		return $response
			->withHeader('Content-Type', 'application/json')
			->withStatus($GLOBALS['responseCode']);
	}
});

$app->delete('/plugin/ipregistration/ip/{id}', function ($request, $response, $args) {
	$ipRegistrationPlugin = new ipRegistrationPlugin();
	if ($ipRegistrationPlugin->auth->checkAccess($ipRegistrationPlugin->config->get("Plugins", "IP-Registration") ['Auth'] ?? "IP-AUTH")) {
		$id = $args['id'] ?? null;
		$ipRegistrationPlugin->api->setAPIResponseData($ipRegistrationPlugin->deleteIPRegistration($id));
	}
	$response->getBody()->write(jsonE($GLOBALS['api']));
	return $response
		->withHeader('Content-Type', 'application/json;charset=UTF-8')
		->withStatus($GLOBALS['responseCode']);
});