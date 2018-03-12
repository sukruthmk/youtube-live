<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use App\Youtube;
use Session;
use DateTime;
use Faker;

class LiveStreamController extends Controller
{
    public function stream(Request $request)  {
        $viewer_data = array();
        $youtube = new Youtube();
        // $viewer_data['broadcast_info'] = "";
        // $viewer_data['broadcast_title'] = "";
        // $viewer_data['broadcast_html'] = "";
        if($request->has('id')) {
            $broadcastId = $request->get('id');
        } else {
            $broadcastsResponse = $youtube->newLiveStream();
            $broadcastId = $broadcastsResponse['id'];
            $liveStreamId = $broadcastsResponse['snippet']['liveChatId'];
        }

        if($request->has('liveChatId')) {
            $liveStreamId = $request->get('liveChatId');
        }

        $broadcastsResponseInfo = $youtube->getBroadcastInfo($broadcastId);
        $viewer_data['broadcast_info'] = $broadcastsResponseInfo;
        $viewer_data['broadcast_title'] = $broadcastsResponseInfo['items'][0]['snippet']['title'];
        $viewer_data['broadcast_html'] = $broadcastsResponseInfo['items'][0]['contentDetails']['monitorStream']['embedHtml'];

        if(!$request->has('liveChatId')) {
            for($i = 0; $i < 3; $i++) {
                $faker = Faker\Factory::create();
                $youtube->postChat($liveStreamId, $faker->text);
            }
        }

        $livechatResponse = $youtube->getChat($liveStreamId);

        foreach($livechatResponse["items"] as $item) {
            $youtubeModel = new Youtube();
            $chat = Youtube::where('chat_id', '=', $item['id'])->exists();
            if(!$chat) {
                $youtubeModel->live_stream_id = $liveStreamId;
                $youtubeModel->chat_id = $item['id'];
                $youtubeModel->usr_name = $item['authorDetails']['displayName'];
                $youtubeModel->msg = $item['snippet']['displayMessage'];
                $youtubeModel->date = $item['snippet']['publishedAt'];

                $youtubeModel->save();
            }
        }


        $viewer_data['live_stream_id'] = $liveStreamId;
        $viewer_data['chats'] = $livechatResponse["items"];
        $viewer_data['broadcast_id'] = $broadcastId;
        // $viewer_data['live_stream_id'] = "";
        // $viewer_data['broadcast_id'] = "";
        // $viewer_data['chats'] = array();
        return view('app.stream', $viewer_data);
    }

    function logout(Request $request) {
        Session::flush();
        return redirect()->route('glogin');
    }

    function search(Request $request) {
        $streamId = $request->get('id');
        $name = $request->get('name');

        $chats = Youtube::where('usr_name', 'LIKE', "%".$name."%")
                         ->where('live_stream_id', '=', $streamId)
                        ->get();

        return response()->json($chats);
    }

    function post(Request $request) {
        $streamId = $request->get('id');
        $msg = $request->get('msg');

        $youtube = new Youtube();
        $messageResource = $youtube->postChat($streamId, $msg);
        $result = $messageResource;
        $livechatResponse = $youtube->getChat($streamId);

        foreach($livechatResponse["items"] as $item) {
            $youtubeModel = new Youtube();
            $youtubeModel->live_stream_id = $streamId;
            $youtubeModel->chat_id = $item['id'];
            $chat = Youtube::where('chat_id', '=', $item['id'])->exists();
            if(!$chat) {
                $youtubeModel->usr_name = $item['authorDetails']['displayName'];
                $youtubeModel->msg = $item['snippet']['displayMessage'];
                $youtubeModel->date = $item['snippet']['publishedAt'];
                $youtubeModel->save();
                $result = $item;
            }
        }

        return response()->json($result);
    }

    function broadcasts(Request $request) {
        $viewer_data = array();
        $youtube = new Youtube();
        $broadcast_response = $youtube->getAllBroadCasts();

        $viewer_data['broadcasts'] = $broadcast_response['items'];
        return view('app.broadcasts', $viewer_data);
    }
}
