<?php
namespace App\Controllers;

use App\Models\User;
use App\Models\Post;
use App\Models\Follower;
use Illuminate\Database\Capsule\Manager as DB;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class DatabaseSeedController extends Controller
{
    /*
     * FOR DEVELOPMENT ONLY: SEED DATABASE
     */
    public function seed(Request $request, Response $response) {
        // Clear database
        $users_count = 50;
        for($i = 1; $i <= $users_count; $i++) {
            $user = [
                'email' => sprintf('user%d@gmail.com', $i),
                'password' => 'p@ssw0rd',
                'first_name' => 'User',
                'last_name' => sprintf('%d', $i)
            ];
            $model = new User();
            $model->email = $user['email'];
            $model->password = password_hash($user['password'], PASSWORD_BCRYPT);
            $model->first_name = $user['first_name'];
            $model->last_name = $user['last_name'];
            $model->save();
        }

        $users = User::all();

        $categories = ['dog', 'cat', 'fish'];
        $posts_count_per_user = 2;
        foreach($users as $user) {
            for($i = 1; $i <= $posts_count_per_user; $i++) {
                $post = [
                    'user_id' => $user->id,
                    'category' => $categories[rand(0, count($categories) - 1)],
                    'content' => sprintf('This is post %d by %s!', $i, $user->first_name . ' ' . $user->last_name)
                ];
                $model = new Post();
                $model->user_id = $post['user_id'];
                $model->content = $post['content'];
                $model->category = $post['category'];
                $model->save();
            }
        }

        $posts = Post::all();
        
        /*
         * Follow some people!
         */
        $follow_mapping = [];
        foreach($users as $user) {
            $key = $user->id;
            
            // Follow 5-10 people each
            $random_limit = rand(5, 10);

            $followed = [];
            for($i = 0; $i < $random_limit; $i++) {
                do {
                    $random_user_id_to_follow = rand($users[0]->id, $users[0]->id + count($users) - 1);
                } while(in_array($random_user_id_to_follow, $followed) || $random_user_id_to_follow == $user->id);

                array_push($followed, $random_user_id_to_follow);
                $follower = new Follower();
                $follower->user_id = $user->id;
                $follower->following_user_id = $random_user_id_to_follow;
                $follower->save();
            }
            $follow_mapping[$key] = $followed;
        }
        $response->getBody()->write($this->encode([
            'err' => 0,
            'users' => $users,
            'following' => $follow_mapping,
            'posts' => $posts
        ]));
        return $response;
    }
}
