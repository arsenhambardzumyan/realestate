<?php

use app\Utils\Router;

$router = new Router();

// Pool routes
$router->add('POST', '/realestate-newpool', 'PoolController@createPool');

// Gift routes
$router->add('POST', '/realestate-addgift', 'GiftController@addGift');

// Ticket routes
$router->add('POST', '/realestate-buytickets', 'TicketController@buyTickets');

$router->add('POST', '/api/login', 'LoginController@login');
$router->add('POST', '/api/logout', 'LoginController@logout');

return $router;
