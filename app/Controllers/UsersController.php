<?php
namespace App\Controllers;

use App\Models\User;
use App\Models\UserSession;
use App\Models\UserVerification;

use Illuminate\Database\Capsule\Manager as DB;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface as Request;


class UsersController extends Controller
{
    private $profile_images_upload_path;

    public function __construct() {
        $this->profile_images_upload_path = uploadsPath('profile-images');
        if(!is_dir($this->profile_images_upload_path)) {
            mkdir($this->profile_images_upload_path, 0777, true); // 3rd parameter true for recursive
        }
    }

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
            /* Create new key for the user's verification via email, save to database, and email the link to the user */
            $key = hash('sha256', $user->email . $user->first_name . time()); /* Hash email, first_name and current time using SHA256 */
            $user_verification = new UserVerification();
            $user_verification->user_id = $user->id;
            $user_verification->verification_key = $key;
            $user_verification->save();
            $url = getenv('APP_URL') . '/account/verify/' . $key;
            $subject = 'Verify your email for Petstonks';
            $from = [getenv('MAIL_ADDR') => 'Petstonks'];
            $to = [$user->email];
            $body = '
            <div class="main" style="display:block;margin:0 auto;max-width: 100%;border:1px solid black;padding:5% 15%;">
                <h2 style="text-align:center;color:black;">Email Verification</h2>
                <p style="font-size:18px;text-align:center;color:black;">Hey there, ' . $user->first_name . '! Thank you for signing up for an account with PetStonks.</p>
                <br>
                <form action="' . $url . '">
                <input type="submit" value="Click to verify" style="display:block;margin:0 auto;text-align:center;background:orange;font-weight:bold;color:white;border-radius:3px;padding:15px;outline:none;border:None;width:200px;cursor:pointer;">
                </form>
            </div>
            ';
            $alternative_body = sprintf('Hey there, %s! Thank you for signing up for an account with PetStonks. Access this link to verify your account: %s', $user->first_name, $url);
            $message = (new \Swift_Message())
                        ->setFrom($from)
                        ->setTo($to)
                        ->setSubject($subject)
                        ->setBody($body, 'text/html')
                        ->addPart($alternative_body, 'text/plain');
            $result = $this->getMailer()->send($message);
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
        $token = $request->getAttribute('_token');
        $user = User::getByToken($token);

        if(!isset($_FILES['profile_image'])) {
            $response->getBody()->write($this->encode([
                'err' => 1,
                'msg' => 'Required field "profile_image" is missing.'
            ]));
            return $response;
        }

        $name = $_FILES["profile_image"]["name"]; /* Get original name */
        $parts = explode(".", $name); /* Extract extension from file name */
        $extension = end($parts);
        $file_name = sprintf('%s.%s', $user->email, $extension); /* New file name: {email}.{extension} */
        $file_save_path = sprintf('%s/%s', $this->profile_images_upload_path, $file_name); /* New file path */
        $file_url = sprintf('/uploads/profile-images/%s', $file_name); /* public URL to retrieve image */
        $tmp_location = $_FILES['profile_image']['tmp_name'];
        move_uploaded_file($tmp_location, $file_save_path);

        /* Update user profile image url */
        $user->profile_image_url = $file_url;
        $user->save();

        $response->getBody()->write($this->encode([
            'err' => 0
        ]));
        return $response;
    }

    public function updateProfile(Request $request, Response $response) {
        $token = $request->getAttribute('_token');
        $user = User::getByToken($token);
        
        $body = $request->getParsedBody();

        $first_name = $this->get($body, 'firstnamechange');
        $last_name = $this->get($body, 'lastnamechange');
        $biography = $this->get($body, 'biographychange');

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

}
