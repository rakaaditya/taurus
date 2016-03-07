<?php 
namespace Controllers;
use Resources, Models, Libraries;

use Twig_Autoloader;
use Twig_Environment;
use Twig_Extension_Debug;
use Twig_Loader_Filesystem;
use Twig_SimpleFunction;

/**
 * This class is for handling base function 
 * for all controllers
 *
 * @package Taurus CMS
 * @author raka aditya <hai@rakaaditya.com>
 * @since version 1.0 <March 2016> 
 */

class BaseController extends Resources\Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->request      = new Resources\Request;
        $this->uri          = new Resources\Uri;
        $this->config       = Resources\Config::main();
        $this->pagination   = new Resources\Pagination;
        $this->uri          = new Resources\Uri;
        $this->session      = new Resources\Session;
        $this->templatePath = getenv('TEMPLATE_PATH');
        $this->blogUrl      = $this->trimSlash(getenv('BLOG_URL'));
        $this->db           = new Resources\Database;
        $this->posts        = new Models\Posts;
        $this->users        = new Models\Users;

        // Twig Factory
        $this->twig         = $this->twigFactory();

    }

    private function twigFactory()
    {
        $loader = new Twig_Loader_Filesystem(APP.'../public/template/'.$this->templatePath);
        $twig   = new Twig_Environment($loader, array('debug' => true));

        $baseUrl = new Twig_SimpleFunction('url', function ($data = '') {
            $uri    = new Resources\Uri;
            return getenv('BLOG_URL').$data;
        });
        

        $assetPath = new Twig_SimpleFunction('assets', function ($data = '') {
            $uri    = new Resources\Uri;
            return $this->blogUrl.'/template/'.$this->templatePath.'/assets/'.$data;
        });

        $trimSlash = new Twig_SimpleFunction('trim_slash', function ($data = '') {
            return $this->trimSlash($data);
        });

        $twig->addFunction($baseUrl);
        $twig->addFunction($assetPath);
        $twig->addFunction($trimSlash);

        $twig->addGlobal('blog_title', getenv('BLOG_TITLE'));
        $twig->addGlobal('blog_description', getenv('BLOG_DESCRIPTION'));
        $twig->addGlobal('menus', $this->menus());
        $twig->addGlobal('archives', $this->archives());

        return $twig;
    }

    private function menus()
    {
        $menus = new Models\Menus;
        return $menus->order('position', 'asc')->get();
    }

    private function archives()
    {
        return $this->posts->getArchives();
    }

    protected function postAll($limit, $offset)
    {
        return $this->posts->getPost($limit, $offset);
    }

    protected function postDetail($year, $month, $day, $slug)
    {
        return $this->posts->getPostDetail($year, $month, $day, $slug);
    }

    protected function pageDetail($slug)
    {
        return $this->posts->getPageDetail($slug);
    }
    
    protected function postMonth($year, $month, $limit, $offset)
    {
        return $this->posts->getPostByMonth($year, $month, $limit, $offset);
    }

    protected function countPostMonth($date)
    {
        return $this->posts->countPostsByMonth($date);
    }

    protected function postUsername($username, $limit, $offset)
    {
        return $this->posts->getPostByUsername($username, $limit, $offset);
    }

    protected function countPostUsername($username)
    {
        return $this->posts->countPostsByUsername($username);
    }

    private function dateToMonth($date)
    {
        return date('F Y', strtotime($date));
    }
    
    private function trimSlash($val)
    {
        return trim($val, '/');
    }

    private function postUrl($date, $slug)
    {
        $date = date('/Y/m/d', strtotime($date));
        return $this->blogUrl.$date.'/'.$slug;
    }

    protected function createPagination($limit, $page)
    {
        $this->pagination = new Resources\Pagination();

        return $this->pagination->setOption([
                'limit'     => $limit,
                'base'      => $this->blogUrl.strtok($_SERVER["REQUEST_URI"],'?').'?page=%#%',
                'total'     => $this->posts->countPost(),
                'current'   => $page,
                'noHref'    => true
        ])
        ->getUrl();
    }
}
