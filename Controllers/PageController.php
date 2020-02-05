<?php

namespace Controllers;

/**
 * Class PageController
 * @package Controllers
 */
class PageController extends Controller
{

    public function home()
    {
        $this->html("home", ["topTen" => $object->items ?? []]);
    }

    public function map()
    {
        if (isset($_SESSION['priv_level'])) {
            if ($_SESSION['priv_level'] > 0) {
                $this->html("map");
            }
        } else {
            header("Location: /login");
            exit(401);        
        }

        
    }
}