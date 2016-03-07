<?php
namespace Controllers;
use Resources, Models;

class PostController extends BaseController
{
    public function detail($year, $month, $day, $slug)
    {
        $post = $this->postDetail($year, $month, $day, $slug);
        
        if(!$post)
            throw new \Resources\HttpException('Page not found!');
        
        echo $this->twig->render('detail.twig', [
            'post' => $post,
            'title' => $post['title'],
        ]);
    }

    public function archive($year, $month)
    {
        $page       = $this->request->get('page') ? (int) $this->request->get('page') : 1;
        $limit      = 5;
        $offset     = ($limit * $page) - $limit;
        $posts      = $this->postMonth($year, $month, $limit, $offset);

        $date = date('F Y', strtotime("$year-$month-01"));

        echo $this->twig->render('archives.twig', [
            'posts'         => $posts,
            'pagination'    => $this->createPagination($limit, $page),
            'date'          => $date,
            'title'         => 'Posts in '.$date
        ]);
    }

    public function author($username)
    {
        $author     = $this->users->getUserByUsername($username);
        $page       = $this->request->get('page') ? (int) $this->request->get('page') : 1;
        $limit      = 5;
        $offset     = ($limit * $page) - $limit;
        $posts      = $this->postUsername($username, $limit, $offset);

        echo $this->twig->render('authors.twig', [
            'posts'         => $posts,
            'pagination'    => $this->createPagination($limit, $page),
            'author'        => $author->name,
            'title'         => 'Posts by '.$author->name
        ]);
    }

    public function page($slug)
    {
        $post = $this->pageDetail($slug);
        
        if(!$post)
            throw new \Resources\HttpException('Page not found!');

        echo $this->twig->render('page.twig', [
            'post'  => $post,
            'title' => $post['title'],
        ]);
    }
}
