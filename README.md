# laravel-vuex
Learn about vuex SPA, laravel 7.0

# Prerequisites
- Install [docker](https://docs.docker.com/engine/install/ubuntu/)
- Install [docker-compose](https://docs.docker.com/compose/install/)
- Window/Mac/ubuntu Os

# Install
- Create env file in web folder
- Fill variables inside env file

```env
DB_CONNECTION=pgsql
DB_HOST=vuesplash_database
DB_PORT=5432
DB_DATABASE=postgres
DB_USERNAME=postgres
DB_PASSWORD=secret

AWS_ACCESS_KEY_ID=[ACCESS_KEY_ID_AWS_IAM]
AWS_SECRET_ACCESS_KEY=[SECRET_KEY_AWS_IAM]
AWS_DEFAULT_REGION=[YOUR_REGION_AWS]
AWS_BUCKET=[BUCKET_NAME]
AWS_URL=https://s3-[YOUR_REGION_AWS].amazonaws.com/[BUCKET_NAME]/
```
- Build with docker
```command
docker-cmpose build
docker-compose up -d
```

- Generate APP_KEY
```command
docker-compose exec vue_web php artisan key:generate
```

- Run server with port 8000
```command
docker-compose exec vue_web php artisan serve --host 0.0.0.0 --port 8000
```

- Run npm run watch
```command
docker-compsoe exec vue-web npm run watch
```

- Migrate table for database
```command
docker-compose exec vue_web php artisan migrate
```
event (
  0 => '__construct',
  1 => 'getText',
  2 => 'getEmojis',
  3 => 'getMessageId',
  4 => 'getMessageType',
  5 => 'getType',
  6 => 'getMode',
  7 => 'getTimestamp',
  8 => 'getReplyToken',
  9 => 'isUserEvent',
  10 => 'isGroupEvent',
  11 => 'isRoomEvent',
  12 => 'isUnknownEvent',
  13 => 'getUserId',
  14 => 'getGroupId',
  15 => 'getRoomId',
  16 => 'getEventSourceId',
)  
bot (
  0 => '__construct',
  1 => 'getProfile',
  2 => 'getMessageContent',
  3 => 'getNumberOfLimitForAdditional',
  4 => 'getNumberOfSentThisMonth',
  5 => 'replyMessage',
  6 => 'replyText',
  7 => 'pushMessage',
  8 => 'multicast',
  9 => 'broadcast',
  10 => 'leaveGroup',
  11 => 'leaveRoom',
  12 => 'parseEventRequest',
  13 => 'validateSignature',
  14 => 'getGroupMemberProfile',
  15 => 'getRoomMemberProfile',
  16 => 'getGroupMemberIds',
  17 => 'getRoomMemberIds',
  18 => 'getAllGroupMemberIds',
  19 => 'getAllRoomMemberIds',
  20 => 'createLinkToken',
  21 => 'getRichMenu',
  22 => 'createRichMenu',
  23 => 'deleteRichMenu',
  24 => 'setDefaultRichMenuId',
  25 => 'getDefaultRichMenuId',
  26 => 'cancelDefaultRichMenuId',
  27 => 'getRichMenuId',
  28 => 'linkRichMenu',
  29 => 'bulkLinkRichMenu',
  30 => 'unlinkRichMenu',
  31 => 'bulkUnlinkRichMenu',
  32 => 'downloadRichMenuImage',
  33 => 'uploadRichMenuImage',
  34 => 'getRichMenuList',
  35 => 'getNumberOfSentReplyMessages',
  36 => 'getNumberOfSentPushMessages',
  37 => 'getNumberOfSentMulticastMessages',
  38 => 'getNumberOfSentBroadcastMessages',
  39 => 'getNumberOfMessageDeliveries',
  40 => 'getNumberOfFollowers',
  41 => 'getFriendDemographics',
  42 => 'getUserInteractionStatistics',
  43 => 'createChannelAccessToken',
  44 => 'revokeChannelAccessToken',
  45 => 'createChannelAccessToken21',
  46 => 'revokeChannelAccessToken21',
  47 => 'getChannelAccessToken21Keys',
  48 => 'sendNarrowcast',
  49 => 'getNarrowcastProgress',
  50 => 'createAudienceGroupForUpdatingUserIds',
  51 => 'updateAudienceGroupForUpdatingUserIds',
  52 => 'createAudienceGroupForClick',
  53 => 'createAudienceGroupForImpression',
  54 => 'renameAudience',
  55 => 'deleteAudience',
  56 => 'getAudience',
  57 => 'getAudiences',
  58 => 'getAuthorityLevel',
  59 => 'updateAuthorityLevel',
)  
```

- Run on browser with host: `[DOCKER_IP]:3002`
