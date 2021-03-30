<?php

namespace App\Controllers;
use Psr\Container\ContainerInterface;

use App\Models\User;
use App\Models\Post;
use App\Models\Follower;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/*
 * Base controller class to be extended by specific controller classes
 */
class Controller
{
    /*
     * Eloquent database
     * @var \Illuminate\Database\Capsule\Manager
     */
    protected $db;
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->db = $container->get('db');
        $this->view = $container->get('view');
    }

    public function doNothing(Request $request, Response $response) {
        return $response;
    }

    protected function encode($content) {
        return json_encode($content, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    /*
     * Gets an array item, with default value of null
     * @var $array array to retrieve value from
     * @var $key array item to get
     */
    protected function get($body, $key) {
        return isset($body[$key]) ? $body[$key] : null;
    }

    /*
     * Writes a base64 string to disk
     */
    protected function base64_write($base64_string, $output_file) {
        // open the output file for writing
        $file_write = fopen($output_file, 'wb'); 
    
        // split the string on commas
        // $data[ 0 ] == "data:image/png;base64"
        // $data[ 1 ] == <actual base64 string>
        $data = explode(',', $base64_string);

        $data = count($data) > 1 ? $data[1] : $data[0];
    
        fwrite($file_write, base64_decode($data));
    
        // clean up the file resource
        fclose($file_write);
    }
}
