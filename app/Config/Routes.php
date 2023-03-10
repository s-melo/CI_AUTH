<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

// Load the system's routing file first, so that the app and ENVIRONMENT
// can override as needed.
if (is_file(SYSTEMPATH . 'Config/Routes.php')) {
    require SYSTEMPATH . 'Config/Routes.php';
}

/*
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Main');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
// The Auto Routing (Legacy) is very dangerous. It is easy to create vulnerable apps
// where controller filters or CSRF protection are bypassed.
// If you don't want to define all routes, please use the Auto Routing (Improved).
// Set `$autoRoutesImproved` to true in `app/Config/Feature.php` and set the following to true.
// $routes->setAutoRoute(false);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.


$routes->get('/',                       'Main::index');


// --------------------------------------------------------------------
// AUTH
// --------------------------------------------------------------------
$routes->get('login_frm/',                                      'User\UserController::login_frm');
$routes->post('login_submit/',                                  'User\UserController::login_submit');

$routes->get('logout/',                                         'User\UserController::logout');

$routes->get('new_user_account_frm',                            'User\UserController::new_user_account_frm');
$routes->post('new_user_account_submit',                        'User\UserController::new_user_account_submit');
$routes->get('verify_email/(:alphanum)',                        'User\UserController::verify_email/$1');

$routes->get('user_recover_password',                           'User\UserController::recover_password_frm');
$routes->post('user_recover_password_submit',                   'User\UserController::recover_password_submit');

$routes->get('user_recover_password_check/(:alphanum)',         'User\UserController::recover_password_check/$1');
$routes->get('user_recover_password_invalid_purl',              'User\UserController::recover_password_invalid_purl');
$routes->post('user_recover_password_define_submit',            'User\UserController::user_recover_password_define_submit');

$routes->get('area1/',                                          'Main::area1');
$routes->get('area2/',                                          'Main::area2');
$routes->get('area3/',                                          'Main::area3');


// TEMP
$routes->get('versessao/',                                      'User\UserController::versessao');
$routes->get('teste/',                                          'Main::teste');
/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (is_file(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
