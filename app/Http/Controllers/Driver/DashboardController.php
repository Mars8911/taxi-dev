<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;

/**
 * 司機儀表板
 */
class DashboardController extends Controller
{
    /**
     * 司機首頁
     */
    public function index()
    {
        return view('driver.dashboard');
    }
}
