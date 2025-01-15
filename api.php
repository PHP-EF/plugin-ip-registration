<?php
// **
// USED TO DEFINE CUSTOM API ROUTES
// **
$app->get('/plugin/ipregistration/settings', function ($request, $response, $args) {
	$ipRegistrationPlugin = new ipRegistrationPlugin();
	if ($ipRegistrationPlugin->auth->checkAccess($ipRegistrationPlugin->auth->checkAccess('Plugins','IP-Registration')['auth'] ?? 'IP-AUTH')) {
		$ipRegistrationPlugin->api->setAPIResponseData($ipRegistrationPlugin->_pluginGetSettings());
	}
	$response->getBody()->write(jsonE($GLOBALS['api']));
	return $response
		->withHeader('Content-Type', 'application/json;charset=UTF-8')
		->withStatus($GLOBALS['responseCode']);
});

$app->get('/plugin/ipregistration/register', function ($request, $response, $args) {
	$ipRegistrationPlugin = new ipRegistrationPlugin();
	if ($ipRegistrationPlugin->auth->checkAccess($ipRegistrationPlugin->auth->checkAccess('Plugins','IP-Registration')['auth'] ?? 'IP-AUTH')) {
		$ipRegistrationPlugin->api->setAPIResponseData($ipRegistrationPlugin->_ipRegistrationPluginIPRegistration());
	}
	$response->getBody()->write(jsonE($GLOBALS['api']));
	return $response
		->withHeader('Content-Type', 'application/json;charset=UTF-8')
		->withStatus($GLOBALS['responseCode']);
});
$app->get('/plugin/ipregistration/update', function ($request, $response, $args) {
	$ipRegistrationPlugin = new ipRegistrationPlugin();
	if ($ipRegistrationPlugin->auth->checkAccess($ipRegistrationPlugin->auth->checkAccess('Plugins','IP-Registration')['auth'] ?? 'IP-AUTH')) {
		$ipRegistrationPlugin->api->setAPIResponseData($ipRegistrationPlugin->_ipRegistrationPluginUpdateFirewall());
	}
	$response->getBody()->write(jsonE($GLOBALS['api']));
	return $response
		->withHeader('Content-Type', 'application/json;charset=UTF-8')
		->withStatus($GLOBALS['responseCode']);
});
$app->get('/plugin/ipregistration/query', function ($request, $response, $args) {
	$ipRegistrationPlugin = new ipRegistrationPlugin();
	if ($ipRegistrationPlugin->auth->checkAccess($ipRegistrationPlugin->auth->checkAccess('Plugins','IP-Registration')['auth'] ?? 'IP-AUTH')) {
		$ipRegistrationPlugin->api->setAPIResponseData($ipRegistrationPlugin->_ipRegistrationPluginQueryIPs());
	}
	$response->getBody()->write(jsonE($GLOBALS['api']));
	return $response
		->withHeader('Content-Type', 'application/json;charset=UTF-8')
		->withStatus($GLOBALS['responseCode']);
});
$app->get('/plugin/ipregistration/list', function ($request, $response, $args) {
	$ipRegistrationPlugin = new ipRegistrationPlugin();
	if ($ipRegistrationPlugin->auth->checkAccess($ipRegistrationPlugin->auth->checkAccess('Plugins','IP-Registration')['auth'] ?? 'IP-AUTH')) {
		$GLOBALS['api'] = $ipRegistrationPlugin->_ipRegistrationPluginListIPs();
	}
	$response->getBody()->write(jsonE($GLOBALS['api']));
	return $response
		->withHeader('Content-Type', 'application/json;charset=UTF-8')
		->withStatus($GLOBALS['responseCode']);
});
$app->delete('/plugin/ipregistration/ip/{id}', function ($request, $response, $args) {
	$ipRegistrationPlugin = new ipRegistrationPlugin();
	if ($ipRegistrationPlugin->auth->checkAccess($ipRegistrationPlugin->auth->checkAccess('Plugins','IP-Registration')['auth'] ?? 'IP-AUTH')) {
		$id = $args['id'] ?? null;
		$ipRegistrationPlugin->api->setAPIResponseData($ipRegistrationPlugin->_ipRegistrationPluginDeleteIP($id));
	}
	$response->getBody()->write(jsonE($GLOBALS['api']));
	return $response
		->withHeader('Content-Type', 'application/json;charset=UTF-8')
		->withStatus($GLOBALS['responseCode']);
});