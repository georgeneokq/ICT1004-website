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
        
        /*
         * Query for posts of the user himself and the accounts the user is following
         */
        $user_id = 1;
        $start = 1;
        $end = 100;
        $end = $end - $start + 1;
        $user1_news_feed = DB::select('SELECT DISTINCT posts.id, posts.user_id, posts.content, posts.category, posts.created_at FROM followers LEFT JOIN posts ON posts.user_id = followers.following_user_id WHERE followers.user_id = ? OR posts.user_id = ? ORDER BY posts.id, posts.user_id, posts.content, posts.category, posts.created_at DESC LIMIT ?, ?', [$user_id, $user_id, $start, $end]);
        $user1_news_feed = array_reverse($user1_news_feed);

        // For each post, get the post_image(s)
        foreach($user1_news_feed as $post) {
            $images = PostImage::where('post_id', $post->id)->get();
            $post->images = $images;
        }
        
        $args = [
            'title' => $title,
            'users' => $users,
            'posts' => $posts,
            'user1_news_feed' => $user1_news_feed
        ];
        return $this->view->render($response, 'home/index.php', $args);
    }
}
