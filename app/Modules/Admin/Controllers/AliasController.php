<?php
namespace Modules\Admin\Controllers;
use Rakaaditya\PanadaRouter\Routes as Route;

class AliasController
{
    public function index()
    {
        $route = new Route;

        // Let's run through the route!!
        $route->run();
    }
}
