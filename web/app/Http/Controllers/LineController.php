<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use LINE\LINEBot\Constant\HTTPHeader;
use LINE\LINEBot;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;
use LINE\LINEBot\SignatureValidator;
use App\Http\Services\LineServices;
use Exception;
use Log;

class LineController extends Controller
{
    /**
     * @var GetMessageService
     */
    private $lineService;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(LineServices $lineService)
    {
        $this->lineService = $lineService;
    }

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
    }
    public function callback(Request $request)
    {
        $signature = $request->headers->get(HTTPHeader::LINE_SIGNATURE);
        if (!SignatureValidator::validateSignature($request->getContent(), config('app.channel_secret'), $signature)) {
            return;
        }
        logger("request : ", $request->all());
        $this->lineService->handleRq($request);
    }
}
