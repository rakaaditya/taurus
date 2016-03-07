<?php
namespace Controllers;
use Resources, Models;

class HomeController extends BaseController
{
    public function index()
    {
    	$page       = $this->request->get('page') ? (int) $this->request->get('page') : 1;
        $limit      = 5;
        $offset     = ($limit * $page) - $limit;
        $posts      = $this->postAll($limit, $offset);

        echo $this->twig->render('home.twig', [
        	'posts'			=> $posts,
        	'pagination'	=> $this->createPagination($limit, $page),
        ]);
    }
}
