<?php
namespace App\Controllers;

use App\Models\User;
use App\Models\UserVerification;
use Illuminate\Database\Capsule\Manager as DB;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface as Request;


class WebController extends Controller
{
    private function redirect(Response $response, $url) {
        $response = $response->withHeader(302);
        return $response->withHeader('Location', $url);
    }
    public function landing(Request $request, Response $response) {
        return $this->view->render($response, 'landing/index.php');
    }

    /* User home page */
    public function home(Request $request, Response $response)
    {
        return $this->view->render($response, 'home/index.php');
    }

    /* For email verification */
    public function verifyAccount(Request $request, Response $response) {
        $params = $request->getQueryParams();
        $key = $this->get($params, 'key');
        if(!$key) {
            return redirect($response, '/');
        }
        $verification = UserVerification::where('verification_key', $key)->first();
        if($verification) {
            /* Mark account as verified, delete the verification record from database, render successful redirect page */
            $user = $verification->user;
            $user->verified = 1;
            $user->save();
            $verification->delete();
            return $this->view->render($response, 'landing/verification_success.php');
        }
    }
}
