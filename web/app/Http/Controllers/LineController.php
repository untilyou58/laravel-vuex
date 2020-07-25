<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\LineServices;

class LineController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public $line;

    public function __construct(LineServices $line)
    {
        $this->line = $line;
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        $httpClient = new \LINE\LINEBot\HTTPClient\CurlHTTPClient(config('app.channel_token'));
        $bot = new \LINE\LINEBot($httpClient, ['channelSecret' => config('app.channel_secret')]);
        return $this->line->register();
    }
}
