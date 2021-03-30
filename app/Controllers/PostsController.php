<?php
namespace App\Controllers;

use App\Models\User;
use App\Models\Post;
use App\Models\PostImage;
use App\Models\PostLike;
use Illuminate\Database\Capsule\Manager as DB;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Http\UploadedFile;


class PostsController extends Controller
{
    private $post_images_upload_path;

    public function __construct() {
        $this->post_images_upload_path = uploadsPath('post-images');
        if(!is_dir($this->post_images_upload_path)) {
            mkdir($this->post_images_upload_path, 0777, true); // 3rd parameter true for recursive
        }
    }

    /*
     * Gets all posts that the user is following.
     * Get limit from query params: Both 'start' and 'end' must be in the query params.
     * For example: /api/news-feed?start=1&end=10   <- This retrieves 1st to 10th post
     */
    public function getNewsFeed(Request $request, Response $response)
    {
        $token = $request->getAttribute('_token');
        $user = User::getByToken($token);
        $params = $request->getQueryParams();
        $start = $this->get($params, 'start');
        $end = $this->get($params, 'end');
        if(!$start || !$end) {
            $response->getBody()->write($this->encode([
                'err' => 1,
                'msg' => '"start" and "end" query parameters are required. For example: /api/news-feed?start=1&end=10'
            ]));
            return $response;
        } else {
            $end = $end - $start + 1;
        }
        
        /*
         * Query for posts of the user himself and the accounts the user is following
         */
        $posts = DB::select('SELECT DISTINCT posts.id, posts.user_id, posts.content, posts.category, posts.created_at FROM followers LEFT JOIN posts ON posts.user_id = followers.following_user_id WHERE followers.user_id = ? OR posts.user_id = ? ORDER BY posts.id, posts.user_id, posts.content, posts.category, posts.created_at DESC LIMIT ?, ?', [$user->id, $user->id, $start, $end]);
        $posts = array_reverse($posts);

        // For each post, get the post_image(s) and user, and whether it is liked by the user
        foreach($posts as $post) {
            $is_liked = PostLike::where(['post_id' => $post->id, 'user_id' => $user->id])->first();
            $post->is_liked = $is_liked ? 1 : 0;
            $num_likes = DB::table('post_likes')
                                ->selectRaw('count(*) AS count')
                                ->where('post_id', $post->id)
                                ->first()->count;
            $post->num_likes = $num_likes;
            $images = PostImage::where('post_id', $post->id)->get();
            $post->images = $images;
            $user = User::find($post->user_id);
            unset($user->password);
            $post->user = $user;
        }

        $response->getBody()->write($this->encode([
            'err' => 0,
            'retrieved_count' => count($posts),
            'posts' => $posts
        ]));

        return $response;
    }

    public function createPost(Request $request, Response $response) {
        $token = $request->getAttribute('_token');
        $user = User::getByToken($token);
        
        $body = $request->getParsedBody();
        
        $category = $this->get($body, 'category');
        $content = $this->get($body, 'content');
        
        $post = new Post();
        $post->user_id = $user->id;
        $post->category = $category;
        $post->content = $content;
        $post->save();
        
        $files_count = isset($_FILES['images']) ? count($_FILES['images']['name']) : 0;
        for($i = 0; $i < $files_count; $i++) {
            $name = $_FILES["images"]["name"][$i];
            $extension = end((explode(".", $name)));
            $file_name = sprintf('%s_%s.%s', $post->id, $i + 1, $extension); // {post_id}_{counter}.{extension}
            $file_save_path = sprintf('%s/%s', $this->post_images_upload_path, $file_name);
            $file_url = sprintf('/uploads/post-images/%s', $file_name); /* Might want to change this to the full site URL */
            $tmp_location = $_FILES['images']['tmp_name'][$i];
            move_uploaded_file($tmp_location, $file_save_path);

            // Create new post image 
            $post_image = new PostImage();
            $post_image->post_id = $post->id;
            $post_image->post_image_url = $file_url;
            $post_image->save();
        }
        $post = Post::with('images')->where('id', $post->id)->first();
        
        $response->getBody()->write($this->encode([
            'err' => 0,
            'post' => $post
        ]));

        return $response;
    }

    public function likePost(Request $request, Response $response) {
        $params = $request->getQueryParams();
        $token = $request->getAttribute('_token');
        $user = User::getByToken($token);
        $post_id = $this->get($params, 'post_id');

        if(!$post_id) {
            $response->getBody()->write($this->encode([
                'err' => 1,
                'msg' => 'Missing query parameter post_id.'
            ]));
            return $response;
        }
        
        // Check if the post is already liked. If yes, return error
        $like = PostLike::where(['post_id' => $post_id, 'user_id' => $user->id])->first();

        if($like) {
            $response->getBody()->write($this->encode([
                'err' => 1,
                'msg' => 'This post is already liked by this user.'
            ]));
            return $response;
        }

        // Check if such a post exists
        $post_exists = Post::find($post_id);
        if(!$post_exists) {
            $response->getBody()->write($this->encode([
                'err' => 1,
                'msg' => 'No such post.'
            ]));
            return $response;
        }

        $like = new PostLike();
        $like->post_id = $post_id;
        $like->user_id = $user->id;
        $like->save();

        $response->getBody()->write($this->encode([
            'err' => 0
        ]));
        return $response;
    }

    public function unlikePost(Request $request, Response $response) {
        $body = $request->getParsedBody();
        $token = $request->getAttribute('_token');
        $user = User::getByToken($token);

        $params = $request->getQueryParams();
        $post_id = $this->get($params, 'post_id');

        if(!$post_id) {
            $response->getBody()->write($this->encode([
                'err' => 1,
                'msg' => 'Missing query parameter post_id.'
            ]));
            return $response;
        }

        $like = PostLike::where(['post_id' => $post_id, 'user_id' => $user->id])->first();

        if(!$like) {
            $response->getBody()->write($this->encode([
                'err' => 1,
                'msg' => 'This post has not been liked by the user.'
            ]));
            return $response;
        }

        $like->delete();
        
        $response->getBody()->write($this->encode([
            'err' => 0,
        ]));
        return $response;
    }
}
