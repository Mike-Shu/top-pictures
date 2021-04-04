<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;

class DashboardController extends Controller
{

    use RefreshDatabase;

    /**
     * @return Application|Factory|View
     */
    public function index()
    {

        return view('pages.dashboard');

    }

}
