<?php

/*
 * Web routes
 * Routes that are meant to return resources to the browser
 */

// Controller namespace shorthand for convenience

$app->get('/', C.'WebController:landing');
$app->get('/home', C.'WebController:home');
$app->get('/account/verify', C.'WebController:verifyAccount');