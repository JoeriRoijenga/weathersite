<?php

namespace Controllers\Admin;

class HomeController extends \Controllers\Controller
{

    public function home()
    {
        $this->html("/Admin/home");
    }
}