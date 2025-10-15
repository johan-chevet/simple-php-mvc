<?php

namespace Src\Controllers;

use Core\Controller;
use Core\Http\Request;

class HomeController extends Controller
{
    public function __construct(Request $request)
    {
        parent::__construct($request);
    }

    public function index()
    {
        return $this->render_with_layout('home/index');
    }
}
