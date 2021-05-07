<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->group(['prefix' => 'api'], function ($router) {
	$router->get('scan-result/{scan}', [
		'as' => 'scan-file-changes', 'uses' => 'ScanDetailsController@getScanResults'
	]);

	$router->get('latest-scan-result', [
		'as' => 'latest-file-changes', 'uses' => 'ScanDetailsController@getLatestScanResults'
	]);

	$router->get('scans', [
		'as' => 'scans', 'uses' => 'ScanDetailsController@getScanList'
	]);

	$router->get('get-version', [
		'as' => 'availble-versions', 'uses' => 'ScanDetailsController@getAvailbleVersionForScan'
	]);

	$router->post('start-scan', [
		'as' => 'availble-versions', 'uses' => 'ScanController@startScanning'
	]);
});

$router->get('/{route:.*}/', function ()  {
    return view('app');
});

