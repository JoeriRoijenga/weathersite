<?php

namespace Controllers;

/**
 * Controller for all the views that are accessible for all users
 * Class PageController
 * @package Controllers
 */
class PageController extends Controller
{
    /**
     * Method for the home page
     */
    public function home()
    {
        $this->html("home", ["topTen" => $object->items ?? []]);
    }

    /**
     * Method for the map page
     */
    public function map()
    {
        if (isset($_SESSION['priv_level']) && $_SESSION['priv_level'] > 0) {
                $this->html("map");
        } else {
            header("Location: /login");
            exit(401);        
        }
    }

    /**
     * Method for the unknown pages
     *
     * @param $status
     */
    public function notFound($status){
        $this->html("404", [
            'status' => $status
        ]);
    }
}