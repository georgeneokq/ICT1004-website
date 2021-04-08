<?php


/*
 * API routes
 * Typical way to return data
 *
 * 1. Get data from data source
 * 2. Encode the data into a json string using json_encode
 * 3. Write the json string into the response body using $response->getBody()->write()
 * 4. Return the response object
 */

use App\Middleware\AuthToken;

use Slim\Routing\RouteCollectorProxy;

// Controller namespace shorthand 'C' for convenience

$app->group('/api', function(RouteCollectorProxy $group) {
    /* ROUTES ONLY FOR TESTING: TO BE REMOVED DURING PRODUCTION */
    $group->get('/users', C.'UsersController:getAllUsers');
    $group->get('/seed', C.'DatabaseSeedController:seed');


    $group->post('/users/signup', C.'UsersController:signup');
    $group->post('/users/login', C.'UsersController:login');
    
    /* ROUTES THAT REQUIRE TOKEN AUTHENTICATION GO HERE!!! */
    $group->group('', function(RouteCollectorProxy $group) {
        $group->post('/users/follow', C.'UsersController:follow');

        $group->get('/users/profile', C.'UsersController:getProfile');
        $group->post('/users/logout', C.'UsersController:logout');
        $group->get('/news-feed', C.'PostsController:getNewsFeed');
        $group->post('/posts/create', C.'PostsController:createPost');
        $group->post('/posts/like', C.'PostsController:likePost');
        $group->delete('/posts/like', C.'PostsController:unlikePost');
        $group->delete('/posts/delete', C.'PostsController:deletePost');
        $group->post('/users/update/profile', C.'UsersController:updateProfile');
        $group->post('/users/update/profile-image', C.'UsersController:updateProfileImage');
        $group->post('/users/updateProfile', C.'UsersController:updateProfile');

    })->add(new AuthToken());
});