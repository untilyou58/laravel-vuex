<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use LINE\LINEBot\Constant\HTTPHeader;
use LINE\LINEBot;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;
use LINE\LINEBot\SignatureValidator;
use Exception;
use Log;
// use App\LineServices;

class LineController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public $line;

    public function __construct()
    {

    }

    // public function __construct(LineServices $line)
    // {
    //     $this->line = $line;
    // }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        $signature = $request->headers->get(HTTPHeader::LINE_SIGNATURE);
        if (!SignatureValidator::validateSignature($request->getContent(), config('app.channel_secret'), $signature)) {
            return;
        }
        $httpClient = new CurlHTTPClient(config('app.channel_token'));
        $bot = new LINEBot($httpClient, ['channelSecret' => config('app.channel_secret')]);
        $textMessageBuilder = new TextMessageBuilder('hello');
        try {
            $events = $bot->parseEventRequest($request->getContent(), $signature);
            foreach ($events as $event) {
                $replyToken = $event->getReplyToken();
                $text = $event->getText();
                $bot->replyText($replyToken, $text);
            }
        } catch (Exception $e) {
            return;
        }
        return;
        // return $this->line->register();
    }
}
