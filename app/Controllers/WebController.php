<?php
namespace App\Controllers;

use App\Models\User;
use App\Models\Post;
use App\Models\PostImage;
use App\Models\PostLike;
use Illuminate\Database\Capsule\Manager as DB;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface as Request;


class WebController extends Controller
{
    public function home(Request $request, Response $response)
    {
        $title = "What's in the database?";
        $users = User::all();
        $posts = Post::with('images')->with('likes')->get();
        foreach($posts as $post) {
            $post->likes_count = count($post->likes);
        }
        $args = [
            'title' => $title,
            'users' => $users,
            'posts' => $posts
        ];
        return $this->view->render($response, 'home/index.php', $args);
    }
}
