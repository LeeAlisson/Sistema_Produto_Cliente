<?php

namespace App\Controllers;

use App\Models\Dashboard;
use App\Url;
use App\View;

class HomeController
{
  public function index(): void
  {
    View::render('home', [
      'stats' => Dashboard::getStats(),
      'pageScripts' => [
        'https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js',
        Url::asset('js/dashboard.js'),
      ],
    ]);
  }
}
