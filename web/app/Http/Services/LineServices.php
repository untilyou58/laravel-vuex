<?php

namespace App\Http\Services;

use Illuminate\Http\Request;
use LINE\LINEBot\Event\MessageEvent;
use LINE\LINEBot\Constant\HTTPHeader;
use LINE\LINEBot;
use LINE\LINEBot\MessageBuilder\StickerMessageBuilder;
use LINE\LINEBot\MessageBuilder\LocationMessageBuilder;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;
use LINE\LINEBot\Event\AccountLinkEvent;
use LINE\LINEBot\Event\BeaconDetectionEvent;
use LINE\LINEBot\Event\FollowEvent;
use LINE\LINEBot\Event\MemberJoinEvent;
use LINE\LINEBot\Event\MemberLeaveEvent;
use LINE\LINEBot\Event\LeaveEvent;
use LINE\LINEBot\Event\JoinEvent;
use LINE\LINEBot\Event\MessageEvent\AudioMessage;
use LINE\LINEBot\Event\MessageEvent\ImageMessage;
use LINE\LINEBot\Event\MessageEvent\LocationMessage;
use LINE\LINEBot\Event\MessageEvent\StickerMessage;
use LINE\LINEBot\Event\MessageEvent\TextMessage;
use LINE\LINEBot\Event\MessageEvent\UnknownMessage;
use LINE\LINEBot\Event\MessageEvent\VideoMessage;
use LINE\LINEBot\Event\PostbackEvent;
use LINE\LINEBot\Event\ThingsEvent;
use LINE\LINEBot\Event\UnfollowEvent;
use LINE\LINEBot\Event\UnknownEvent;
use LINE\LINEBot\Exception\InvalidEventRequestException;
use LINE\LINEBot\Exception\InvalidSignatureException;
use LINE\LINEBot\SignatureValidator;
use Exception;
use Log;

class LineServices
{
    /**
     * @var LINEBot
     */
    protected $bot;
    
    public function __construct()
    {
        $httpClient = new CurlHTTPClient(config('app.channel_token'));
        $this->bot = new LINEBot($httpClient, ['channelSecret' => config('app.channel_secret')]);
    }

    public function handleRq(Request $request)
    {
        $signature = $request->headers->get(HTTPHeader::LINE_SIGNATURE);
        if (!SignatureValidator::validateSignature($request->getContent(), config('app.channel_secret'), $signature)) {
            return;
        }
        $bot = $this->bot;
        $receives = $bot->parseEventRequest($request->getContent(), $signature);
        foreach ($receives as $receive) {
            if ($receive instanceof MessageEvent) {
                if ($receive instanceof TextMessage) {
                    $text = $receive->getText();
                    $bot->replyText($receive->getReplyToken(), $text);
                } elseif ($receive instanceof ImageMessage) {
                    $contentProvider = $receive->getContentProvider();
                    $replyToken = $receive->getReplyToken();
                    if ($contentProvider->isExternal()) {
                        $this->bot->replyMessage(
                            $replyToken,
                            new ImageMessageBuilder(
                                $contentProvider->getOriginalContentUrl(),
                                $contentProvider->getPreviewImageUrl()
                            )
                        );
                        return;
                    }
                    $contentId = $receive->getMessageId();
                    $image = $bot->getMessageContent($contentId)->getRawBody();
                    $filePath = $_SERVER['DOCUMENT_ROOT'] . sha1(time()) . '.jpg';
                    $filename = basename($filePath);
            
                    $fh = fopen($filePath, 'x');
                    fwrite($fh, $image);
                    fclose($fh);
                } elseif ($receive instanceof VideoMessage) {
                    // TODO
                } elseif ($receive instanceof AudioMessage) {
                    $bot->replyText($receive->getReplyToken(), "Thank you for sending a audio.");
                } elseif ($receive instanceof LocationMessage) {
                    $replyToken = $receive->getReplyToken();
                    $title = $receive->getTitle();
                    $address = $receive->getAddress();
                    $latitude = $receive->getLatitude();
                    $longitude = $receive->getLongitude();
            
                    $bot->replyMessage(
                        $replyToken,
                        new LocationMessageBuilder($title, $address, $latitude, $longitude)
                    );
                } elseif ($receive instanceof StickerMessage) {
                    $bot->replyMessage(
                        $receive->getReplyToken(),
                        new StickerMessageBuilder('1', '2')
                    );
                }  else {
                    throw new Exception("Received invalid message type");
                }
            } elseif ($receive instanceof UnfollowEvent) {
                // TODO
            } elseif ($receive instanceof FollowEvent) {
                // TODO
            } elseif ($receive instanceof MemberJoinEvent) {
                
            } elseif ($receive instanceof JoinEvent) {
                // TODO
            } elseif ($receive instanceof LeaveEvent) {
                // TODO
            } elseif ($receive instanceof MemberLeaveEvent) {
                
            } elseif ($receive instanceof PostbackEvent) {
                // TODO
            } elseif ($receive instanceof BeaconDetectionEvent) {
                // TODO
            } elseif ($receive instanceof AccountLinkEvent) {
                // TODO
            } elseif ($receive instanceof ThingsEvent) {
                // TODO
            } elseif ($receive instanceof UnknownEvent) {
                // TODO
            } else {
                logger("Exception : ", $receive->getType());
                throw new Exception("Received invalid receive type");
            }
            Log::info(print_r($receive, true));
        }
        return ;
    }
}