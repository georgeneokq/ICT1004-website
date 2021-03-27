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
        $posts = DB::select('SELECT posts.id, posts.user_id, posts.content, posts.category, posts.created_at FROM followers LEFT JOIN posts ON posts.user_id = followers.following_user_id WHERE followers.user_id = ? OR posts.user_id = ? ORDER BY posts.created_at DESC LIMIT ?, ?', [$user->id, $user->id, $start, $end]);
        
        // For each post, get the post_image(s)
        foreach($posts as $post) {
            $images = PostImage::where('post_id', $post->id)->get();
            $post->images = $images;
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
        $uploadedFiles = $request->getUploadedFiles();
        $images = isset($uploadedFiles['images']) ? $uploadedFiles['images'] : [];

        $post = new Post();
        $post->user_id = $user->id;
        $post->category = $category;
        $post->content = $content;
        $post->save();

        
        foreach($images as $counter=>$image) {
            $extension = pathinfo($image->getClientFilename(), PATHINFO_EXTENSION);
            $file_name = sprintf('%s_%s.%s', $post->id, $counter + 1, $extension); // {post_id}_{counter}.{extension}
            $file_save_path = sprintf('%s/%s', $this->post_images_upload_path, $file_name);
            $file_url = sprintf('/uploads/post-images/%s', $file_name); /* Might want to change this to the full site URL */
            $image->moveTo($file_save_path);

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
        $body = $request->getParsedBody();
        $token = $request->getAttribute('_token');
        $user = User::getByToken($token);
        $post_id = $this->get($body, 'post_id');
        
        // Check if the post is already liked. If yes, return error
        $like = PostLike::where(['post_id' => $post_id, 'user_id' => $user->id])->first();

        if($like) {
            $response->getBody()->write($this->encode([
                'err' => 1,
                'msg' => 'This post is already liked by this user.'
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
