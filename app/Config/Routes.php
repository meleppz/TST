<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Home::index');

// Customer Service API
$routes->get('customers', 'CustomerAPI::index');
$routes->get('customers/(:num)', 'CustomerAPI::show/$1');
$routes->get('customers/(:num)/summary', 'CustomerAPI::summary/$1');

// Product Service API
$routes->get('products', 'ProductAPI::index');
$routes->get('products/(:num)', 'ProductAPI::show/$1');
$routes->get('categories', 'ProductAPI::categories');
