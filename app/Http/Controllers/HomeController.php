<?php

namespace App\Http\Controllers;

use OA\PathItem;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    //
    /**
     * @PathItem(path="/api")
     *
     * @Info(
     *      version="0.0.0",
     *      title="Anophel API Documentation"
     *  )
     */
    public function index(): string
    {
        return "API";
    }
}
