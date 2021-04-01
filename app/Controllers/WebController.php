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
    public function landing(Request $request, Response $response) {
        return $this->view->render($response, 'landing/index.php');
    }

    /* User home page */
    public function home(Request $request, Response $response)
    {
        return $this->view->render($response, 'home/index.php');
    }
}
