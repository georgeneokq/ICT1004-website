<?php
namespace App\Controllers;

use App\Models\User;
use App\Models\UserSession;

use Illuminate\Database\Capsule\Manager as DB;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface as Request;


class UsersController extends Controller
{
    public function getAllUsers(Request $request, Response $response) {
        $users = User::all();
        $response->getBody()->write($this->encode($users));
        return $response;
    }

    public function signup(Request $request, Response $response) {
        $body = $request->getParsedBody();
        $email = $this->get($body, 'email');
        $password = $this->get($body, 'password');
        $first_name = $this->get($body, 'first_name');
        $last_name = $this->get($body, 'last_name');
        $biography = $this->get($body, 'biography');

        // Check if the user exists. If it does, send an appropriate error message back
        $user = User::where('email', $email)->first();
        if($user) {
            $response->getBody()->write($this->encode([
                'err' => 1,
                'msg' => 'This email is already registered.'
            ]));
            return $response;
        }

        // Create user model and save to database
        $user = new User();
        $user->email = $email;
        $user->password = password_hash($password, PASSWORD_BCRYPT);
        $user->first_name = $first_name;
        $user->last_name = $last_name;
        $user->biography = $biography;
        if($user->save()) {
            $response->getBody()->write($this->encode([
                'err' => 0
            ]));
        } else {
            $response->getBody()->write($this->encode([
                'err' => 1,
                'msg' => 'An error has occurred.'
            ]));
        }
        return $response;
    }

    public function login(Request $request, Response $response) {
        $body = $request->getParsedBody();
        // Get email and password
        $email = $this->get($body, 'email');
        $password = $this->get($body, 'password');

        /* TODO: DO FIELD VALIDATION! */

        // Get model by email
        $user = User::where('email', $email)->first();

        if($user) {
            // Check hash
            if(password_verify($password, $user->password)) {
                $session = $user->renewToken();
                $token = $session->token;
                $expires_at = $session->expires_at;

                // Remove "password" and "activated" field from user and return to client
                unset($user->password);
                unset($user->activated);
                
                $response->getBody()->write($this->encode([
                    'err' => 0,
                    '_token' => $token,
                    '_token_expiry' => $expires_at,
                    'user' => $user
                ]));
                return $response;
            }
        }
        /* Validation failed. Don't give too much information about the invalid attempt, in case of hacking attempt */
        $response->getBody()->write($this->encode([
            'err' => 1,
            'msg' => 'Invalid credentials',
            'email' => $body
        ]));
        return $response;
    }

    public function logout(Request $request, Response $response) {
        $body = $request->getParsedBody();
        $token = $request->getAttribute('_token');
        $user_session = UserSession::where('token', $token)->first();

        if($user_session->delete()) {
            $response->getBody()->write($this->encode([
                'err' => 0
            ]));
        } else {
            $response->getBody()->write($this->encode([
                'err' => 1,
                'msg' => 'Unable to logout of this account'
            ]));
        }

        return $response;
    }

    public function getProfile(Request $request, Response $response) {
        $token = $request->getAttribute('_token');
        $user = User::getByToken($token);
        unset($user->password);
        $response->getBody()->write($this->encode([
            'err' => 0,
            'user' => $user
        ]));
        return $response;
    }

    public function updateProfileImage(Request $request, Response $response) {
        $profile_image = $this->get($request->getUploadedFiles(), 'profile_image'); // Might need a [0] here.

        /* TODO: REPLACE WITH ACTUAL URL ONCE INTERGRATED WITH S3BUCKET */
        $profile_image_url = 'https://animeshelter.com/wp-content/uploads/2020/11/jujutsu-kaisen-episode-7-2455.jpg';
    }
}
