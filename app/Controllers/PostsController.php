<?php
namespace App\Controllers;

use App\Models\User;
use App\Models\Post;
use Illuminate\Database\Capsule\Manager as DB;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class PostsController extends Controller
{
    /*
     * Gets all posts that the user is following
     */
    public function getNewsFeed(Request $request, Response $response)
    {
        $token = $request->getAttribute('_token');
        $user = User::getByToken($token);
        
        /*
         * For testing, /api/seed route, which calls the DatabaseSeedController's methods to insert fake data for testing.
         * 
         */
        $posts = DB::select('SELECT posts.user_id, posts.content, posts.category, posts.created_at FROM followers LEFT JOIN posts ON posts.user_id = followers.following_user_id WHERE followers.user_id = ?', [$user->id]);
        $response->getBody()->write($this->encode([
            'err' => 0,
            'posts' => $posts
        ]));

        return $response;
    }
}
