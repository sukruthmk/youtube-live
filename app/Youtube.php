<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use DateTime;
use Faker;
use Session;


class Youtube extends Model
{
    protected $gClient = false;
    protected $yClient = false;
    protected $table = 'youtube';

    function __construct() {
        $this->initializeClient();
    }

    function initializeClient() {
        if(!$this->gClient) {
            $google_redirect_url = route('glogin');
            $gClient = new \Google_Client();
            $gClient->setApplicationName(config('services.google.app_name'));
            $gClient->setClientId(config('services.google.client_id'));
            $gClient->setClientSecret(config('services.google.client_secret'));
            $gClient->setRedirectUri($google_redirect_url);
            $gClient->setDeveloperKey(config('services.google.api_key'));
            $gClient->setScopes(array(
                'https://www.googleapis.com/auth/youtube'
            ));

            if (session('token'))
            {
                $gClient->setAccessToken(session('token'));
            }

            $youtube = new \Google_Service_YouTube($gClient);

            $this->gClient = $gClient;
            $this->yClient = $youtube;
        }
    }

    function newLiveStream() {
        $client = $this->gClient;
        $youtube = $this->yClient;
        $broadcastsResponse = array();
        $faker = Faker\Factory::create();
        $randomString = $faker->name . " " . $faker->unique()->randomDigit;
        try {
            $broadcastSnippet = new \Google_Service_YouTube_LiveBroadcastSnippet();
            $broadcastSnippet->setTitle('broadcast - ' . $randomString);
            $currentdatetime = new DateTime();
            $broadcastSnippet->setScheduledStartTime($currentdatetime->format(DateTime::ATOM));
            // $broadcastSnippet->setScheduledEndTime('2034-01-31T00:00:00.000Z');

            // Create an object for the liveBroadcast resource's status, and set the
            // broadcast's status to "private".
            $status = new \Google_Service_YouTube_LiveBroadcastStatus();
            $status->setPrivacyStatus('public');

            // Create the API request that inserts the liveBroadcast resource.
            $broadcastInsert = new \Google_Service_YouTube_LiveBroadcast();
            $broadcastInsert->setSnippet($broadcastSnippet);
            $broadcastInsert->setStatus($status);
            $broadcastInsert->setKind('youtube#liveBroadcast');

            // Execute the request and return an object that contains information
            // about the new broadcast.
            $broadcastsResponse = $youtube->liveBroadcasts->insert('snippet,status', $broadcastInsert, array());
            $youtube_event_id = $broadcastsResponse['id'];
            /**
             * Call the API's videos.list method to retrieve the video resource.
             */
            $listResponse = $youtube->videos->listVideos("snippet", array('id' => $youtube_event_id));
            $video = $listResponse[0];
            //live stream
            // Create an object for the liveStream resource's snippet. Specify a value
            // for the snippet's title.
            $streamSnippet = new \Google_Service_YouTube_LiveStreamSnippet();
            $streamSnippet->setTitle('stream -'.$randomString);

            // Create an object for content distribution network details for the live
            // stream and specify the stream's format and ingestion type.
            $cdn = new \Google_Service_YouTube_CdnSettings();
            $cdn->setFormat("1080p");
            $cdn->setIngestionType('rtmp');
            // Create the API request that inserts the liveStream resource.
            $streamInsert = new \Google_Service_YouTube_LiveStream();
            $streamInsert->setSnippet($streamSnippet);
            $streamInsert->setCdn($cdn);
            $streamInsert->setKind('youtube#liveStream');
            $streamStatus = new \Google_Service_YouTube_LiveStreamStatus();
            $streamStatus->setStreamStatus("active");
            $streamInsert->setStatus($streamStatus);
            // Execute the request and return an object that contains information
            // about the new stream.
            $streamsResponse = $youtube->liveStreams->insert('snippet,cdn,status', $streamInsert, array());
            // Bind the broadcast to the live stream.
            $bindBroadcastResponse = $youtube->liveBroadcasts->bind(
                    $broadcastsResponse['id'], 'id,contentDetails', array(
                'streamId' => $streamsResponse['id'],
            ));
        } catch (Exception $e) {
            if ($e->getCode() == 401) {
                header("Location: public/index.php");
            }
            die($e->getMessage());
        }

        return $broadcastsResponse;
    }

    function getBroadcastInfo($broadcastId) {
        $client = $this->gClient;
        $youtube = $this->yClient;
        try {
            $broadcastResponse = $youtube->liveBroadcasts->listLiveBroadcasts('id,snippet,contentDetails,status', array('id' => $broadcastId));
        } catch (Exception $e) {
            if ($e->getCode() == 401) {
                header("Location: public/index.php");
            }
            die($e->getMessage());
        }
        return $broadcastResponse;
    }

    function postChat($liveChatId, $message) {
        $client = $this->gClient;
        $youtube = $this->yClient;
        try {
            $chatMessage = new \Google_Service_YouTube_LiveChatMessage();
            $chatSnippet = new \Google_Service_YouTube_LiveChatMessageSnippet();
            $chatSnippet->setLiveChatId($liveChatId);
            $textMessageDetails = new \Google_Service_YouTube_LiveChatTextMessageDetails();
            $textMessageDetails->setMessageText($message);
            $chatSnippet->setTextMessageDetails($textMessageDetails);
            $chatSnippet->setType("textMessageEvent");
            $chatMessage->setSnippet($chatSnippet);
            $messageResource = $youtube->liveChatMessages->insert("snippet", $chatMessage);
        } catch (Exception $e) {
            die($e->getMessage());
        }

        return $messageResource;
    }

    function getChat($chatId, $optParams = array()) {
        $client = $this->gClient;
        $youtube = $this->yClient;
        try {
            $livechatResponse = $youtube->liveChatMessages->listLiveChatMessages($chatId, "id,snippet,authorDetails", $optParams);
        } catch (Exception $e) {
            if ($e->getCode() == 401) {
                header("Location: public/index.php");
            }
            die($e->getMessage());
        }

        return $livechatResponse;
    }

    function getAllBroadCasts() {
        $client = $this->gClient;
        $youtube = $this->yClient;

        $broadcastsResponse = array();
        try {
            // Execute an API request that lists broadcasts owned by the user who
            // authorized the request.
            $broadcastsResponse = $youtube->liveBroadcasts->listLiveBroadcasts(
                    'id,snippet,contentDetails,status', array(
                'mine' => 'true'
            ));
        } catch (Google_Service_Exception $e) {
            if($e->getCode() == 401) {
                header("Location: public/index.php");
            }
            die($e->getMessage());
        } catch (Google_Exception $e) {
            die($e->getMessage());
        }

        return $broadcastsResponse;
    }
}
