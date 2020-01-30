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
        $this->html("home");
    }

    public function precipitation()
    {
        $this->html("precipitation");
    }

    public function map()
    {
        $this->html("map");
    }
}