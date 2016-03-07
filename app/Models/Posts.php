<?php
namespace Models;
use Resources;

class Posts extends Resources\ActiveRecord
{
    
    public function __construct()
    {   
        call_user_func_array( 'parent::__construct', func_get_args() );    
    }

    public function getPost($limit, $offset)
    {
        $posts =  $this->db->results("SELECT * FROM posts 
            LEFT JOIN users ON user_id = posts.user_id 
            WHERE posts.deleted_at IS NULL
            AND posts.published_at <= NOW()
            AND posts.is_page = 0
            ORDER BY posts.id DESC 
            LIMIT {$limit} OFFSET {$offset}");
        
        return $this->createPostLists($posts);
    }

    public function countPost()
    {
        return $this->db->getVar("SELECT COUNT(id) FROM posts");
    }

    public function getPostByMonth($year, $month, $limit, $offset)
    {
        $posts =  $this->db->results("SELECT * FROM posts 
            LEFT JOIN users ON user_id = posts.user_id 
            WHERE YEAR(posts.published_at) = {$year} 
            AND MONTH(posts.published_at) = {$month}
            AND posts.deleted_at IS NULL
            AND posts.published_at <= NOW()
            AND posts.is_page = 0
            ORDER BY posts.id DESC 
            LIMIT {$limit} OFFSET {$offset}");
        
        return $this->createPostLists($posts);
    }

    public function countPostsByMonth($date)
    {
        $year = date('Y', strtotime($date));
        $month = date('m', strtotime($date));

        return $this->db->getVar("SELECT COUNT(id) FROM posts 
            WHERE YEAR(published_at) = {$year} 
            AND MONTH(published_at) = {$month} 
            AND deleted_at IS NULL
            AND published_at <= NOW()");
    }

    public function getPostByUsername($username, $limit, $offset)
    {
        $posts =  $this->db->results("SELECT * FROM posts 
            LEFT JOIN users ON user_id = posts.user_id 
            WHERE users.username = '{$username}'
            AND posts.deleted_at IS NULL
            AND posts.published_at <= NOW()
            AND posts.is_page = 0
            ORDER BY posts.id DESC 
            LIMIT {$limit} OFFSET {$offset}");
        
        return $this->createPostLists($posts);
    }

    public function countPostsByUsername($username)
    {

        return $this->db->getVar("SELECT COUNT(id) FROM posts 
            LEFT JOIN users ON users.id = posts.user_id 
            WHERE users.username = '{$username}'
            AND posts.deleted_at IS NULL
            AND posts.published_at <= NOW()
            AND posts.is_page = 0");
    }

    public function getArchives()
    {
        $archives = $this->db->results("SELECT * FROM posts 
            WHERE posts.deleted_at IS NULL
            AND posts.published_at <= NOW()
            AND posts.is_page = 0
            GROUP BY MONTH(published_at) 
            ORDER BY published_at DESC");

        $archiveData = [];

        if($archives)
            foreach($archives as $archive)
                $archiveData[] = [
                    'month' => $this->dateToMonth($archive->published_at),
                    'total' => $this->countPostsByMonth($archive->published_at),
                    'url'   => '/archives/'.date('Y/m', strtotime($archive->published_at)),
                ];

        return $archiveData;
    }

    public function getPostDetail($year, $month, $day, $slug)
    {
        $post = $this->db->row("SELECT * FROM posts 
            LEFT JOIN users ON user_id = posts.user_id 
            WHERE YEAR(posts.published_at) = {$year} 
            AND MONTH(posts.published_at) = {$month} 
            AND DAY(posts.published_at) = {$day} 
            AND posts.slug = '{$slug}' 
            AND posts.deleted_at IS NULL
            AND posts.published_at <= NOW()
            AND posts.is_page = 0");

        return $this->createPostDetail($post);
    }

    public function getPageDetail($slug)
    {
        $post = $this->db->row("SELECT * FROM posts 
            LEFT JOIN users ON user_id = posts.user_id 
            WHERE posts.slug = '{$slug}' 
            AND posts.deleted_at IS NULL
            AND posts.published_at <= NOW()
            AND posts.is_page = 1");
        
        return $this->createPostDetail($post);
    }

    private function postUrl($date, $slug)
    {
        $date = date('/Y/m/d', strtotime($date));
        return $date.'/'.$slug;
    }

    private function createPostLists($posts)
    {
        $postData   = [];
        if($posts)
            foreach ($posts as $post) {
                $postData[] = [
                    'id'            => $post->id,
                    'title'        => $post->title,
                    'summary'       => $post->summary,
                    'content'       => $post->content,
                    'created_at'    => $post->created_at,
                    'published_at'  => date('l, d M Y H:i', strtotime($post->published_at)),
                    'cover_image'   => $post->cover_image,
                    'slug'          => $post->slug,
                    'url'           => $this->postUrl($post->published_at, $post->slug),
                    'author'          => [
                        'id'        => $post->user_id,
                        'name'      => $post->name,
                        'username'  => $post->username,
                        'avatar'    => $post->avatar
                    ]
                ];
            }

        return $postData;
    }

    private function createPostDetail($post)
    {
        $postData = [];

        if($post)
            $postData = [
                'id'            => $post->id,
                'title'         => $post->title,
                'summary'       => $post->summary,
                'content'       => $post->content,
                'created_at'    => $post->created_at,
                'published_at'  => date('l, d M Y H:i', strtotime($post->published_at)),
                'cover_image'   => $post->cover_image,
                'slug'          => $post->slug,
                'url'           => $this->postUrl($post->published_at, $post->slug),
                'author'          => [
                    'id'        => $post->user_id,
                    'name'      => $post->name,
                    'username'  => $post->username,
                    'avatar'    => $post->avatar
                ]
            ];

        return $postData;
    }

    private function dateToMonth($date)
    {
        return date('F Y', strtotime($date));
    }
}
