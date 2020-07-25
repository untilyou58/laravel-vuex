<?php

/**
 * Copyright 2016 LINE Corporation
 *
 * LINE Corporation licenses this file to you under the Apache License,
 * version 2.0 (the "License"); you may not use this file except in compliance
 * with the License. You may obtain a copy of the License at:
 *
 *   https://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations
 * under the License.
 */

namespace App\LineServices;

use LINE\LINEBot;
use LINE\LINEBot\Constant\HTTPHeader;
use LINE\LINEBot\Event\AccountLinkEvent;
use LINE\LINEBot\Event\BeaconDetectionEvent;
use LINE\LINEBot\Event\FollowEvent;
use LINE\LINEBot\Event\JoinEvent;
use LINE\LINEBot\Event\LeaveEvent;
use LINE\LINEBot\Event\MessageEvent;
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
use App\LineServices\EventHandler\BeaconEventHandler;
use App\LineServices\EventHandler\FollowEventHandler;
use App\LineServices\EventHandler\JoinEventHandler;
use App\LineServices\EventHandler\LeaveEventHandler;
use App\LineServices\EventHandler\MessageHandler\AudioMessageHandler;
use App\LineServices\EventHandler\MessageHandler\ImageMessageHandler;
use App\LineServices\EventHandler\MessageHandler\LocationMessageHandler;
use App\LineServices\EventHandler\MessageHandler\StickerMessageHandler;
use App\LineServices\EventHandler\MessageHandler\TextMessageHandler;
use App\LineServices\EventHandler\MessageHandler\VideoMessageHandler;
use App\LineServices\EventHandler\PostbackEventHandler;
use App\LineServices\EventHandler\ThingsEventHandler;
use App\LineServices\EventHandler\UnfollowEventHandler;

class Route
{
    public function register(\Slim\App $app)
    {
            /** @var LINEBot $bot */
            $bot = $this->bot;
            /** @var \Monolog\Logger $logger */
            $logger = $this->logger;

            $signature = $req->getHeader(HTTPHeader::LINE_SIGNATURE);
            if (empty($signature)) {
                $logger->info('Signature is missing');
                return $res->withStatus(400, 'Bad Request');
            }

            try {
                $events = $bot->parseEventRequest($req->getBody(), $signature[0]);
            } catch (InvalidSignatureException $e) {
                $logger->info('Invalid signature');
                return $res->withStatus(400, 'Invalid signature');
            } catch (InvalidEventRequestException $e) {
                return $res->withStatus(400, "Invalid event request");
            }

            foreach ($events as $event) {
                /** @var EventHandler $handler */
                $handler = null;

                if ($event instanceof MessageEvent) {
                    if ($event instanceof TextMessage) {
                        $handler = new TextMessageHandler($bot, $logger, $req, $event);
                    } elseif ($event instanceof StickerMessage) {
                        $handler = new StickerMessageHandler($bot, $logger, $event);
                    } elseif ($event instanceof LocationMessage) {
                        $handler = new LocationMessageHandler($bot, $logger, $event);
                    } elseif ($event instanceof ImageMessage) {
                        $handler = new ImageMessageHandler($bot, $logger, $req, $event);
                    } elseif ($event instanceof AudioMessage) {
                        $handler = new AudioMessageHandler($bot, $logger, $req, $event);
                    } elseif ($event instanceof VideoMessage) {
                        $handler = new VideoMessageHandler($bot, $logger, $req, $event);
                    } elseif ($event instanceof UnknownMessage) {
                        $logger->info(sprintf(
                            'Unknown message type has come [message type: %s]',
                            $event->getMessageType()
                        ));
                    } else {
                        // Unexpected behavior (just in case)
                        // something wrong if reach here
                        $logger->info(sprintf(
                            'Unexpected message type has come, something wrong [class name: %s]',
                            get_class($event)
                        ));
                        continue;
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
                    // Unexpected behavior (just in case)
                    // something wrong if reach here
                    $logger->info(sprintf(
                        'Unexpected event type has come, something wrong [class name: %s]',
                        get_class($event)
                    ));
                    continue;
                }

                $handler->handle();
            }

            $res->write('OK');
            return $res;
    }
}