<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->get('/', 'home'); 
$routes->resource('pegawai');
$routes->resource('karyawan');
