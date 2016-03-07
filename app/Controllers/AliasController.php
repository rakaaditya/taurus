<?php
namespace Controllers;
use Rakaaditya\PanadaRouter\Routes as Route;

class AliasController
{
    public function index()
    {
        $route = new Route;

        $route->get('home', 'HomeController@index');
        $route->get('{year}/{month}/{day}/{slug}', 'PostController@detail');
        $route->get('archives/{year}/{month}', 'PostController@archive');
        $route->get('author/{username}', 'PostController@author');

        // Let's run through the route!!
        $route->run();
    }
}
