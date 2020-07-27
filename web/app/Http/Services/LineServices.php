<?php

namespace App\Http\Services;

use Illuminate\Http\Request;
use LINE\LINEBot\Event\MessageEvent;
use LINE\LINEBot\Constant\HTTPHeader;
use LINE\LINEBot;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;
use LINE\LINEBot\Event\AccountLinkEvent;
use LINE\LINEBot\Event\BeaconDetectionEvent;
use LINE\LINEBot\Event\FollowEvent;
use LINE\LINEBot\Event\JoinEvent;
use LINE\LINEBot\Event\LeaveEvent;
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
use LINE\LINEBot\KitchenSink\EventHandler\BeaconEventHandler;
use LINE\LINEBot\KitchenSink\EventHandler\FollowEventHandler;
use LINE\LINEBot\KitchenSink\EventHandler\JoinEventHandler;
use LINE\LINEBot\KitchenSink\EventHandler\LeaveEventHandler;
use LINE\LINEBot\KitchenSink\EventHandler\MessageHandler\AudioMessageHandler;
use LINE\LINEBot\KitchenSink\EventHandler\MessageHandler\ImageMessageHandler;
use LINE\LINEBot\KitchenSink\EventHandler\MessageHandler\LocationMessageHandler;
use LINE\LINEBot\KitchenSink\EventHandler\MessageHandler\StickerMessageHandler;
use LINE\LINEBot\KitchenSink\EventHandler\MessageHandler\TextMessageHandler;
use LINE\LINEBot\KitchenSink\EventHandler\MessageHandler\VideoMessageHandler;
use LINE\LINEBot\KitchenSink\EventHandler\PostbackEventHandler;
use LINE\LINEBot\KitchenSink\EventHandler\ThingsEventHandler;
use LINE\LINEBot\KitchenSink\EventHandler\UnfollowEventHandler;
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
                } elseif ($receive instanceof ImageMessage || $receive instanceof VideoMessage) {
                    $content = $bot->getMessageContent($receive->getContentId());
                    $meta = stream_get_meta_data($content->getFileHandle());
                    $contentSize = filesize($meta['uri']);
                    $type = $receive->isImage() ? 'image' : 'video';
                    $previewContent = $bot->getMessageContentPreview($receive->getContentId());
                    $previewMeta = stream_get_meta_data($previewContent->getFileHandle());
                    $previewContentSize = filesize($previewMeta['uri']);
                    $bot->replyText(
                        $receive->getReplyToken(),
                        "Thank you for sending a $type.\nOriginal file size: " .
                        "$contentSize\nPreview file size: $previewContentSize"
                    );
                } elseif ($receive instanceof AudioMessage) {
                    $bot->replyText($receive->getReplyToken(), "Thank you for sending a audio.");
                } elseif ($receive instanceof LocationMessage) {
                    $bot->sendLocation(
                        $receive->getReplyToken(),
                        sprintf("%s\n%s", $receive->getText(), $receive->getAddress()),
                        $receive->getLatitude(),
                        $receive->getLongitude()
                    );
                } elseif ($receive instanceof StickerMessage) {
                    $bot->sendSticker(
                        $receive->getReplyToken(),
                        $receive->getStkId(),
                        $receive->getStkPkgId(),
                        $receive->getStkVer()
                    );
                }  else {
                    throw new Exception("Received invalid message type");
                }
            } elseif ($event instanceof UnfollowEvent) {
                $handler = new UnfollowEventHandler($bot, $logger, $event);
            } elseif ($event instanceof FollowEvent) {
                $handler = new FollowEventHandler($bot, $logger, $event);
            } elseif ($event instanceof JoinEvent) {
                $handler = new JoinEventHandler($bot, $logger, $event);
            } elseif ($event instanceof LeaveEvent) {
                $handler = new LeaveEventHandler($bot, $logger, $event);
            } elseif ($event instanceof PostbackEvent) {
                $handler = new PostbackEventHandler($bot, $logger, $event);
            } elseif ($event instanceof BeaconDetectionEvent) {
                $handler = new BeaconEventHandler($bot, $logger, $event);
            } elseif ($event instanceof AccountLinkEvent) {
                $handler = new AccountLinkEventHandler($bot, $logger, $event);
            } elseif ($event instanceof ThingsEvent) {
                $handler = new ThingsEventHandler($bot, $logger, $event);
            } elseif ($event instanceof UnknownEvent) {
                $logger->info(sprintf('Unknown message type has come [type: %s]', $event->getType()));
            } else {
                throw new Exception("Received invalid receive type");
            }
        }
        return ;
    }
}