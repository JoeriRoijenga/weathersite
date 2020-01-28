<?php

namespace Controllers;

/**
 * Class HomeController
 * @package Controllers
 */
class HomeController extends Controller
{

    public function home()
    {
        $this->html("home");
    }
}