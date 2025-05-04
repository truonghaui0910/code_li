<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Common\Youtube;

use App\Common\Network\ProxyHelper;
use App\Common\Network\RequestHelper;
use App\Common\Utils;
use Illuminate\Support\Facades\Config;
use Log;

/**
 * Description of YoutubeHelper
 *
 * @author hoabt2
 */
class YoutubeHelper {

    //put your code here
    public static function processMutiSource($links) {
        $separate = Config::get('config.separate_text');
        $arrTmp = explode($separate, $links);
        $arrResult = array();
        foreach ($arrTmp as $link) {
            $arrResult[] = self::processSource($link);
        }
        return json_encode($arrResult);
    }

    public static function processSource($link) {
        //0: keyword
        //1: playlist,channel
        //2: video
        $type = 0;
        if (strpos($link, "youtu.be") !== FALSE) {
            $type = 2;
            $regexYoutubeId = "/youtu.be\/(?:(.+?)\?|(.+)$)/";
            preg_match($regexYoutubeId, $link, $matches);
            if (count($matches) > 0) {
                $link = $matches[count($matches) - 1];
            }
        }
        if (strpos($link, "youtube.") !== FALSE) {
            //url
            $type = 1;
            if (strpos($link, "/user/") !== FALSE) {
                $response = RequestHelper::get($link, 1);
                $regexChannelId = "/name='channel_ids'\\s+value=\"(.+?)\"/";
                preg_match($regexChannelId, $response, $matches);
                if (count($matches) == 2) {
                    $link = preg_replace("/UC/i", "UU", $matches[1], 1);
                }
            } else {
                $regexPLChannelId = "/(?:list=|channel\/)(?:(.+?)&|(.+)$)/";
                preg_match($regexPLChannelId, $link, $matches);
                if (count($matches) > 1) {
                    $link = preg_replace("/UC/i", "UU", $matches[count($matches) - 1], 1);
                } else {
                    $regexVideoId = "/v=(?:(.+?)&|(.+)$)/";
                    preg_match($regexVideoId, $link, $matches);
                    $type = 2;
                    if (count($matches) > 0) {
                        $link = $matches[count($matches) - 1];
                    }
                }
            }
        }
        return array("type" => $type, "id" => $link);
    }

    private static function findVid($data, $pattern) {
        preg_match_all($pattern, $data, $matchers);
        $arrTmp = array();
        if (isset($matchers) && count($matchers) > 1) {
            $arrTmp = array_unique($matchers[1]);
        }
        return $arrTmp;
    }

    private static function findNextLink($data, $pattern, $isFirst = false) {
        preg_match_all($pattern, $data, $matchers);
        if (count($matchers[1]) > 0) {
            if ($isFirst) {
                return "https://m.youtube.com/" . $matchers[1][0];
            } else {
                return isset($matchers[1][1]) ? "https://m.youtube.com/" . $matchers[1][1] : null;
            }
        }
        return null;
    }

    public static function getVideoDieFromPlaylist($playlistId, $maxPage = 100) {
        $url = "https://m.youtube.com/playlist?list=" . $playlistId;
        $arrResult = array();
        $isFirst = true;
        $countPage = 0;
        $regex = "/watch\?list=.+v=(.*?)(&|\")/i";
        $regexNext = '/(playlist\?list=.+&amp;ctoken=.+?)\"/i';
        $indexThumbnail = 0;
        while (isset($url)) {
            $response = RequestHelper::get($url, 1);
            $indexThumbnail = strpos($response, "no_thumbnail-", $indexThumbnail);
            while ($indexThumbnail !== FALSE) {
                $indexFirstA = strripos(substr($response, 0, $indexThumbnail), "<a");
                $indexLastA = strpos($response, "</a>", $indexFirstA);
                $resTmp = substr($response, $indexFirstA, $indexLastA - $indexFirstA);
                $arrResult = array_merge($arrResult, self::findVid($resTmp, $regex));
                $indexThumbnail = strpos($response, "no_thumbnail-", $indexThumbnail + 13);
            }
            if (++$countPage == $maxPage) {
                break;
            }
            $url = self::findNextLink($response, $regexNext, $isFirst);
            $isFirst = false;
        }
        return $arrResult;
    }

    public static function getVideoFromPlaylist($playlistId, $maxPage = 100) {
        $url = "https://m.youtube.com/playlist?list=" . $playlistId;
        $arrResult = array();
        $isFirst = true;
        $countPage = 0;
        $regex = "/watch\?list=.+v=(.*?)(&|\")/i";
        $regexNext = '/(playlist\?list=.+&amp;ctoken=.+?)\"/i';

        while (isset($url)) {
            error_log("count: $countPage");
            $response = RequestHelper::get($url, 1);
            $arrResult = array_merge($arrResult, self::findVid($response, $regex));
            if (++$countPage == $maxPage) {
                break;
            }
            $url = self::findNextLink($response, $regexNext, $isFirst);
            $isFirst = false;
        }
        return $arrResult;
    }

    public static function checkVideoInPlaylist($videoSearch, $playlistId) {
        $index = -1;
        $url = "https://m.youtube.com/playlist?list=" . $playlistId;
        $arrResult = array();
        $isFirst = true;
        $countPage = 0;
        $regex = "/watch\?list=.+v=(.*?)&/i";
        $regexNext = '/(playlist\?list=.+&amp;ctoken=.+?)\"/i';
        $indexBias = 0;
        while (isset($url)) {
            $response = RequestHelper::get($url, 1);
            $arrResult = self::findVid($response, $regex);
            $index = array_search($videoSearch, $arrResult);
            if ($index === FALSE) {
                $index = -1;
            } else {
                $index += $indexBias;
                break;
            }
            $indexBias += count($arrResult);
            $url = self::findNextLink($response, $regexNext, $isFirst);
            $isFirst = false;
        }
        return $index;
    }

    public static function searchVideo($keyword, $maxPage = 10, $uploaded_type = 1) {
        /*
         * uploaded
         * 1 '': all
         * 2 d: today
         * 3 w: this week
         * 4 m: this month
         */
        $uploaded = '';
        switch ($uploaded_type) {
            case 1:$uploaded = '';
                break;
            case 2:$uploaded = 'd';
                break;
            case 3:$uploaded = 'w';
                break;
            case 4:$uploaded = 'm';
                break;
            default :
                $uploaded = '';
                break;
        }
        $keyword = str_replace(" ", "+", $keyword);
        $uploaded = '';
        $url = "https://m.youtube.com/results?uploaded=$uploaded&sp=EgIQAVAU&q=" . $keyword . "&submit=Search";
        $regex = "/watch\?v=(.+?)&/i";
        $regexNext = "/(results\?.*action_continuation.+?)\"/i";
        $isFirst = true;
        $arrResult = array();
        $countPage = 0;
        while (isset($url)) {
            $response = RequestHelper::get($url, 1);
            $arrResult = array_merge($arrResult, self::findVid($response, $regex));
            if (++$countPage == $maxPage) {
                break;
            }
            $url = self::findNextLink($response, $regexNext, $isFirst);
            $isFirst = false;
        }
        return $arrResult;
    }

    public static function getPlaylistInfo($playlistId) {
        $patternTitle = "/pl-header-title.*?>(.+?)</is";
        $patternDetails = "/pl-header-details.+?<\/a>.*?<li>([\d\.]*).+?<\/li>.*?<li>([\d\.,]*).+?<\/li>/i";
        $response = RequestHelper::get("https://www.youtube.com/playlist?list=$playlistId", 0);
        $number_video = 0;
        $number_views = 0;
        $playlistIdNew = $playlistId;
        $channelName = "";
        $playlistName = "";
        $status = 2; //not exists
        if (strpos($response, $playlistId) !== FALSE) {
            $status = 0;
            preg_match($patternDetails, $response, $matchers);
            // preg_match($patternTitle, $response, $matcherTitles);
            if (count($matchers) > 1) {
                $status = 1;
                $tmp = str_replace(",", "", $matchers[1]);
                $tmp = str_replace(".", "", $tmp);
                if (is_numeric($tmp)) {
                    $number_video = intval($tmp);
                }
                $tmp = str_replace(",", "", $matchers[2]);
                $tmp = str_replace(".", "", $tmp);
                if (is_numeric($tmp)) {
                    $number_views = intval($tmp);
                }
            }
        }
        return array("status" => $status, "id" => $playlistId, "channelName" => $channelName, "playlistName" => $playlistName, "numberVideo" => $number_video, "numberView" => $number_views);
    }

    public static function getVideoInfoVer2($videoId, $urlClient = null) {
        if (isset($urlClient)) {
            $response = RequestHelper::getWithIp("http://www.youtube.com/get_video_info?video_id=$videoId", $urlClient, 1);
        } else {
            $response = RequestHelper::get("http://www.youtube.com/get_video_info?video_id=$videoId", 1);
        }
        parse_str($response, $arrData);
        $status = 0;
        $views = 0;
        $videoLength = 0;
        $resVideo = 'small';
        $like = 0;
        $dislike = 0;
        $publishDateText = '';
        $publishDate = 0;
        if (isset($arrData) && count($arrData) > 0) {
            if ($arrData['status'] === 'ok') {
                $status = 1;
                $views = $arrData['view_count'];
                $videoLength = $arrData['length_seconds'];
                $fmt_list = $arrData['fmt_list'];
                if (Utils::containString($fmt_list, "1280x720")) {
                    $resVideo .= ',hd';
                }
                if (Utils::containString($fmt_list, "640x360/9/0/115")) {
                    $resVideo .= ',sd';
                }
            }
        }
        return array($status, $videoLength, $like, $dislike, $views, $publishDateText, $publishDate, $resVideo);
    }

    public static function getVideoInfo($videoId, $urlClient = null) {
        if (isset($urlClient)) {
            $response = RequestHelper::getWithIp("https://m.youtube.com/watch?v=$videoId", $urlClient, 1);
        } else {
            $response = RequestHelper::get("https://m.youtube.com/watch?v=$videoId", 1);
        }
        $status = 0;
        $videoLength = 0;
        $title = "";
        $like = 0;
        $dislike = 0;
        $views = 0;
        $publishDateText = "";
        $publishDate = 0;
        $indexStart = strpos($response, "searchForm");
        $indexStart = strpos($response, "<table>", $indexStart);
        if (strpos($response, "Started streaming") > 0) {
            $status = 0;
        } else {
            $status = $indexStart > 0 ? 1 : 0;
        }
        if ($status == 1) {
            preg_match("/<title>(.+)<\/title>/", $response, $matches);
            if (isset($matches[1])) {
                $title = $matches[1];
            }
            $indexEnd = strpos($response, "</table>", $indexStart);
            $response = substr($response, $indexStart, $indexEnd - $indexStart);
            $arrTmp = explode("</div>", $response);
            preg_match("/(?:(\d+):)?(\d+):(\d+)/", $arrTmp[0], $matches);
            if (isset($matches[1]) && is_numeric($matches[1])) {
                $videoLength += $matches[1] * 3600;
            }
            if (isset($matches[2])) {
                $videoLength += $matches[2] * 60;
            }
            if (isset($matches[3])) {
                $videoLength += $matches[3];
            }
            $arrTmp2 = explode("</span>", $arrTmp[0]);
            preg_match("/>(\d+)/", $arrTmp2[0], $matches2);
            if (isset($matches2[1])) {
                $like = intval($matches2[1]);
            }
            preg_match("/>(\d+)/", $arrTmp2[1], $matches3);
            if (isset($matches3[1])) {
                $dislike = intval($matches3[1]);
            }

            preg_match("/([\d,]+)/", $arrTmp[1], $matches4);
            if (isset($matches4[1])) {
                $views = intval(str_replace(",", "", $matches4[1]));
            }

            preg_match("/(?:Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Oct|Nov|Dec)\s*(?:[1-9]|0[1-9]|[1-2][0-9]|3[01]),\s*(?:19|20)\d\d/i", $arrTmp[3], $matches5);
            if (isset($matches5[0])) {
                $d = \DateTime::createFromFormat('M d, Y', $matches5[0]);
                $publishDate = date_format($d, "Ym");
            } else {
                $publishDate = date_format(new \DateTime(), "Ym");
            }
        }
        Log::info($response);
        return array("status" => $status, "title" => $title, "length" => $videoLength, "like" => $like, "dislike" => $dislike, "view" => $views, "publish_date" => $publishDate);
    }

    public static function getVideoInfoInPlayList($id) {
        $urlPage = "https://content.googleapis.com/youtube/v3/playlistItems?id=$id&part=snippet&key=" . Utils::getKeyYoutube();
        $data = file_get_contents($urlPage);
        $data = json_decode($data);
    }

    public static function getChannelInfo($channelId, $urlClient = null) {
        if (isset($urlClient)) {
            $response = RequestHelper::getWithIp("https://m.youtube.com/channel/$channelId/about", $urlClient, 1);
        } else {
            $response = RequestHelper::get("https://m.youtube.com/channel/$channelId/about", 1);
        }
        $status = -1;
        $subscribes = 0;
        $views = 0;
        $regex = "";
        if (strpos($response, "youtube") !== FALSE) {
            if (strpos($response, $channelId) === FALSE) {
                $status = 0;
            } else {
                $status = 1;
                preg_match("/([\d,]+)\s+views/", $response, $matches);
                if (isset($matches[1])) {
                    $views = $matches[1];
                    $views = str_replace(",", "", $views);
                }
                preg_match("/([\d,]+)\s+subscribers/", $response, $matches);
                if (isset($matches[1])) {
                    $subscribes = $matches[1];
                    $subscribes = str_replace(",", "", $subscribes);
                }
            }
        } else {
            $status = 1;
        }
        return array("status" => $status, "subscribes" => $subscribes, "views" => $views);
    }

//    public static function download($url, $extension) {
//        $now = time();
//        $tmpInt = rand(1, 100);
//        $nameVid = "$now-$tmpInt.$extension";
//        $filePath = PATH_DOWNLOAD . $nameVid;
//        try {
//            $commandDownload = "youtube-dl --ffmpeg-location /xcxcx/ffmpeg -o  $filePath '$url'";
//            if (strpos($url, 'youtube') !== false) {
//                $commandDownload = "youtube-dl --ffmpeg-location /xcxcx/ffmpeg -f mp4 -o  $filePath '$url'";
//            }
//            shell_exec($commandDownload);
//            if (!Utils::checkFileOK($filePath)) {
//                unlink($filePath);
//                throw new Exception("Blocked Video");
//            }
//        } catch (Exception $ex) {
//            throw $ex;
//        }
//        return $filePath;
//    }
    public static function processSourceFacebook($link) {

        $response = RequestHelper::get("https://graph.facebook.com/v2.12/" . $link . "/videos?limit=1000&access_token=EAAAAUaZA8jlABAPLUcwlqxF8Ho2oHqPO11KE07jApl6e4ZB1YO53p8T0GlM0XZCYlTzTqIrHZBYI5xUEypSLZAJPN4zeFepMSI5d2IUvOsSnmRdoWIgVqCf1hQTFWEXiawNYK7IHHjHMFzZBhpzcyNHZCKHZBWSxmPvvcA8EnvV1iAZDZD", 1);
        return $response;
    }

    public static function getChannelInfoV2($channelId, $test = 0) {
//        if (isset($urlClient)) {
//            $response = RequestHelper::getWithIp("https://m.youtube.com/channel/$channelId/about", $urlClient, 1);
//        } else {
//            $response = RequestHelper::get("https://m.youtube.com/channel/$channelId/about", 1);
//        }
        $status = 0;
        $link = "https://m.youtube.com/channel/$channelId/about";
        if (Utils::containString($channelId, "@") || Utils::containString($channelId, "/c/")) {
            $link = "https://m.youtube.com/$channelId/about";
        }
        $response = ProxyHelper::get($link, 1);
        $out = preg_replace_callback("(\\\\x([0-9a-f]{2}))i", function($a) {
            return chr(hexdec($a[1]));
        }, $response
        );
        $content = "";
        preg_match_all("/<script(.*?)<\/script>/", $out, $matches0);
        if (count($matches0) > 0) {
            $arrays = $matches0[1];
            foreach ($arrays as $data) {
                if (Utils::containString($data, "var ytInitialData =")) {
                    preg_match("/var\s+ytInitialData\s+=\s+'(.*?)';/", $out, $matches1);
                    $content = str_replace('\\\\', '\\', $matches1[1]);
                }
            }
        }

        $subscribers = 0;
        $views = 0;
        $channelName = '';
        $date = 0;
        $avatar = "";
        $banner = "";
        $channel = "";
        $videoCountText = 0;
        $handle = "";
        preg_match("/\"contents\"/", $content, $matches);
        if ($test == 1) {
            Utils::write("save/channel.txt", $content);
        }
        if (count($matches) > 0) {
            $data = json_decode($content);
            $status = 1;
//            preg_match("/\"subscriberCountText\":{\"runs\":\[{\"text\":\"([^\"]+)\"/", $content, $matchesSub);
//            if (isset($matchesSub[1])) {
//                $subscribes = Utils::shortNumber2Number(rtrim($matchesSub[1], 'subcribers'));
//            }
//            if (!empty($data->header->c4TabbedHeaderRenderer->subscriberCountText->runs[0]->text)) {
//                $subscribes = Utils::shortNumber2Number(rtrim($data->header->c4TabbedHeaderRenderer->subscriberCountText->runs[0]->text, 'subcribers'));
//            }
//            preg_match("/\"viewCountText\":{\"runs\":\[{\"text\":\"([^\"]+)\"/", $content, $matchesvView);
//            if (isset($matchesvView[1])) {
//                $views = Utils::getNumberFromText($matchesvView[1]);
//            }
            if (!empty($data->onResponseReceivedEndpoints[0]->showEngagementPanelEndpoint
                            ->engagementPanel->engagementPanelSectionListRenderer
                            ->content->sectionListRenderer->contents[0]->itemSectionRenderer->contents[0]->aboutChannelRenderer->metadata
                            ->aboutChannelViewModel)) {
                $about = $data->onResponseReceivedEndpoints[0]
                        ->showEngagementPanelEndpoint->engagementPanel->engagementPanelSectionListRenderer
                        ->content->sectionListRenderer->contents[0]->itemSectionRenderer->contents[0]->aboutChannelRenderer->metadata
                        ->aboutChannelViewModel;
                if (!empty($about->viewCountText)) {
                    $views = Utils::getNumberFromText($about->viewCountText);
                }
                if (!empty($about->subscriberCountText)) {
                    $subscribers = Utils::getNumberFromText($about->subscriberCountText);
                }
                if (!empty($about->channelId)) {
                    $channel = $about->channelId;
                }
                if (!empty($about->videoCountText)) {
                    $videoCountText = Utils::getNumberFromText($about->videoCountText);
                }
                if (!empty($about->joinedDateText->content)) {
                    $tmpDate = $about->joinedDateText->content;
                    $date = strtotime(trim(str_replace("Joined", "", $tmpDate)));
                }
            }

            if (!empty($data->metadata->channelMetadataRenderer->title)) {
                $channelName = $data->metadata->channelMetadataRenderer->title;
            }
            if (!empty($data->metadata->channelMetadataRenderer->avatar->thumbnails[0]->url)) {
                $avatar = $data->metadata->channelMetadataRenderer->avatar->thumbnails[0]->url;
            }
            if (!empty($data->header->pageHeaderRenderer->content->pageHeaderViewModel->banner->imageBannerViewModel->image->sources)) {
                $count = count($data->header->pageHeaderRenderer->content->pageHeaderViewModel->banner->imageBannerViewModel->image->sources);
                $banner = $data->header->pageHeaderRenderer->content->pageHeaderViewModel->banner->imageBannerViewModel->image->sources[$count - 1]->url;
            }
            if (!empty($data->header->pageHeaderRenderer->content->pageHeaderViewModel->metadata->contentMetadataViewModel->metadataRows[0]->metadataParts[0]->text->content)) {
                $handle = $data->header->pageHeaderRenderer->content->pageHeaderViewModel->metadata->contentMetadataViewModel->metadataRows[0]->metadataParts[0]->text->content;
            }
            
//            if (!empty($data->onResponseReceivedEndpoints[0]->showEngagementPanelEndpoint
//                            ->engagementPanel->engagementPanelSectionListRenderer
//                            ->content->sectionListRenderer->contents[0]->itemSectionRenderer->contents[0]->aboutChannelRenderer->metadata
//                            ->aboutChannelViewModel->viewCountText)) {
//                $views = Utils::getNumberFromText($data->onResponseReceivedEndpoints[0]
//                                ->showEngagementPanelEndpoint->engagementPanel->engagementPanelSectionListRenderer
//                                ->content->sectionListRenderer->contents[0]->itemSectionRenderer->contents[0]->aboutChannelRenderer->metadata
//                                ->aboutChannelViewModel->viewCountText);
//            }
//            if (!empty($data->onResponseReceivedEndpoints[0]->showEngagementPanelEndpoint
//                            ->engagementPanel->engagementPanelSectionListRenderer
//                            ->content->sectionListRenderer->contents[0]->itemSectionRenderer->contents[0]->aboutChannelRenderer->metadata
//                            ->aboutChannelViewModel->subscriberCountText)) {
//                $subscribes = Utils::getNumberFromText($data->onResponseReceivedEndpoints[0]
//                                ->showEngagementPanelEndpoint->engagementPanel->engagementPanelSectionListRenderer
//                                ->content->sectionListRenderer->contents[0]->itemSectionRenderer->contents[0]->aboutChannelRenderer->metadata
//                                ->aboutChannelViewModel->subscriberCountText);
//            }
//            preg_match("/\"joinedDateText\":{\"runs\":\[{\"text\":\"Joined\s+\"},{\"text\":\"([a-zA-Z0-9\s,]+)\"/", $content, $matchesJoin);
//            if (isset($matchesJoin[1])) {
//                $date = strtotime(trim($matchesJoin[1]));
//            }
//            preg_match("/\"header\":{\"c4TabbedHeaderRenderer\":{\"channelId\":\"[a-zA-Z0-9\-\_\=\+]+\",\"title\":\"([^\"]+)\"/", $content, $matchesName);
//            if (isset($matchesName[1])) {
//                $channelName = trim($matchesName[1]);
//            }
//            if (!empty($data->header->c4TabbedHeaderRenderer->avatar->thumbnails)) {
//                $count = count($data->header->c4TabbedHeaderRenderer->avatar->thumbnails);
//                $avatar = $data->header->c4TabbedHeaderRenderer->avatar->thumbnails[$count - 1]->url;
//            }
//            if (!empty($data->header->c4TabbedHeaderRenderer->tvBanner->thumbnails)) {
//                $count = count($data->header->c4TabbedHeaderRenderer->tvBanner->thumbnails);
//                $banner = $data->header->c4TabbedHeaderRenderer->tvBanner->thumbnails[$count - 1]->url;
//            }
//            if (!empty($data->header->c4TabbedHeaderRenderer->channelId)) {
//                $channelId = $data->header->c4TabbedHeaderRenderer->channelId;
//            }
        }
        return array("status" => $status,
            "subscribers" => $subscribers,
            "views" => $views,
            'date' => $date,
            'handle' => $handle,
            'channelId' => $channel,
            'channelName' => $channelName,
            "avatar" => $avatar,
            "banner" => $banner
        );
    }

    public static function getPlaylist($playlistId, $get = 50, $pageToken = "", $retries = 3) {
//        error_log("getPlaylist");
        $list_video_id = array();
        $list_video_name = array();
        $list_date = array();
        $curl = null;
        $numberGet = 50;
        try {
            if ($get == 0) {
                $numberGet = 50;
            } else {
                $numberGet = $get;
            }
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://content.googleapis.com/youtube/v3/playlistItems?maxResults=$numberGet&"
                . "part=snippet&pageToken=$pageToken&playlistId=$playlistId&key=AIzaSyB-f6x6zibodhxkkVndoAeKkvNNXzrPRWs",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET"
            ));
            $response = curl_exec($curl);

            if (!isset($response) || $response == "") {
                if ($retries < 1) {
                    echo("Error getPlaylist:--- ");
                } else {
                    echo("Retry getPlaylist:--- ");
                    return self::getPlaylist($playlistId, $pageToken, --$retries);
                }
            }
            curl_close($curl);
            $datas = json_decode($response);
            if (!empty($datas->items)) {
                if (isset($datas->nextPageToken)) {
                    $pageToken = $datas->nextPageToken;
                } else {
                    $pageToken = null;
                }
                foreach ($datas->items as $item) {
                    array_push($list_video_id, $item->snippet->resourceId->videoId);
                    array_push($list_video_name, $item->snippet->title);
                    array_push($list_date, str_replace("-", "", substr($item->snippet->publishedAt, 0, 10)));
                }
            }
            if ($get != 0) {
                if (count($list_video_id) < $get) {
                    $more = $get - count($list_video_id);
                    if (isset($pageToken) && !empty($datas->items)) {
                        $arrTmp = self::getPlaylist($playlistId, $more, $pageToken);
                        $list_video_id = array_merge($list_video_id, $arrTmp["list_video_id"]);
                        $list_video_name = array_merge($list_video_name, $arrTmp["list_video_name"]);
                        $list_date = array_merge($list_date, $arrTmp["list_date"]);
//                $list_video = array_merge($list_video, $arrTmp["list_video"]);
                    }
                }
            } else {
                if (isset($pageToken) && !empty($datas->items)) {
                    $arrTmp = self::getPlaylist($playlistId, 0, $pageToken);
                    $list_video_id = array_merge($list_video_id, $arrTmp["list_video_id"]);
                    $list_video_name = array_merge($list_video_name, $arrTmp["list_video_name"]);
                    $list_date = array_merge($list_date, $arrTmp["list_date"]);
//                $list_video = array_merge($list_video, $arrTmp["list_video"]);
                }
            }
        } catch (Exception $e) {
            try {
                if (isset($curl)) {
                    curl_close($curl);
                }
            } catch (Exception $exc) {
                
            }
            if ($retries < 1) {
                echo("Error getPlaylist: " . $e->getMessage());
            } else {
                echo("Retry getPlaylist: " . $e->getMessage());
                return self::getPlaylist($playlistId, $pageToken, --$retries);
            }
        }
        return array("list_video_id" => $list_video_id, "list_video_name" => $list_video_name, "list_date" => $list_date);
    }

    public static function getVideoInfoHtmlDesktop($videoId, $test = 0) {
        $response = ProxyHelper::get("https://youtube.com/watch?v=$videoId", 2);
        $status = 0;
        $videoLength = 0;
        $title = "";
        $like = 0;
        $dislike = 0;
        $views = 0;
        $publishDateText = "";
        $publishDate = 0;
        $channelId = "";
        $channelName = "";
        $songnames = [];
        $artists = [];
        $albums = [];
        $licenses = [];
        $writers = [];
        $comment = 0;
        $countSong = 0;
        $nextVideo = "";
        $channel_sub = 0;
        $description = "";

        $out = preg_replace_callback("(\\\\x([0-9a-f]{2}))i", function($a) {
            return chr(hexdec($a[1]));
        }, $response
        );
        preg_match("/<meta itemprop=\"duration\" content=\"([^\"]+)/", $response, $mat);
        if (count($mat) > 1) {
            $tmpDur = $mat[1];
            $tmpDur = str_replace("PT", "", $tmpDur);
            $tmpDur = str_replace("S", "", $tmpDur);
            $arr = explode("M", $tmpDur);
            if (count($arr) == 2) {
                $videoLength = $arr[0] * 60 + $arr[1];
            }
        }
        $content = "";
        preg_match_all("/<script(.*?)<\/script>/", $out, $matches0);
        if (count($matches0) > 0) {
            $arrays = $matches0[1];
            foreach ($arrays as $data) {
                if (Utils::containString($data, "var ytInitialData =")) {
                    $data .= 'xxxyyyzzz';
                    preg_match("/var\s+ytInitialData\s+=\s+(.*?);xxxyyyzzz/", $data, $matches1);
                    $content = str_replace('', '\\', $matches1[1]);
                }
            }
        } else {
            error_log("getVideoInfoHtmlDesktop $videoId not matches0");
        }
        preg_match("/\"contents\"/", $content, $matches);
//        $content = str_replace("/\\", "/\\\\", $content);
        if ($test == 1) {
            Utils::write("save/video.txt", $content);
        }
        if (count($matches) > 0) {
            $datas = json_decode($content);
            if (!empty($datas->contents->twoColumnWatchNextResults->results->results->contents)) {
//                $status = 1;
                $temps = $datas->contents->twoColumnWatchNextResults->results->results->contents;
                foreach ($temps as $temp) {
                    if (!empty($temp->videoPrimaryInfoRenderer)) {
                        $info = $temp->videoPrimaryInfoRenderer;

                        if (!empty($info->title->runs[0]->text)) {
                            $title = $info->title->runs[0]->text;
                        }

                        if (!empty($info->viewCount->videoViewCountRenderer->viewCount->simpleText)) {
                            $views_tmp = $info->viewCount->videoViewCountRenderer->viewCount->simpleText;
                            $views = Utils::getNumberFromText(trim($views_tmp));
                        }
                        if (!empty($info->dateText->simpleText)) {
                            $publishDateText = $info->dateText->simpleText;
                            $publishDateText = trim(str_replace("Published on", "", str_replace("Premiered", "", $publishDateText)));
                            $publishDate = strtotime($publishDateText);
                        }

                        if (!empty($info->videoActions->menuRenderer->topLevelButtons[0]->segmentedLikeDislikeButtonRenderer->likeButton->toggleButtonRenderer->defaultText->simpleText)) {
                            $like = Utils::shortNumber2Number($info->videoActions->menuRenderer->topLevelButtons[0]->segmentedLikeDislikeButtonRenderer->likeButton->toggleButtonRenderer->defaultText->simpleText);
                        }
                        if (!empty($info->videoActions->menuRenderer->topLevelButtons[1]->toggleButtonRenderer->defaultText->simpleText)) {
                            $dislike = intval($info->videoActions->menuRenderer->topLevelButtons[1]->toggleButtonRenderer->defaultText->simpleText);
                        }
                    }
                    if (!empty($temp->videoSecondaryInfoRenderer)) {
                        $info = $temp->videoSecondaryInfoRenderer;
                        if (!empty($info->owner->videoOwnerRenderer->title->runs[0]->text)) {
                            $channelName = $info->owner->videoOwnerRenderer->title->runs[0]->text;
                        }
                        if (!empty($info->owner->videoOwnerRenderer->navigationEndpoint->browseEndpoint->browseId)) {
                            $channelId = $info->owner->videoOwnerRenderer->navigationEndpoint->browseEndpoint->browseId;
                        }
                        if (!empty($info->owner->videoOwnerRenderer->subscriberCountText->simpleText)) {
                            $channel_sub = Utils::getNumberFromText($info->owner->videoOwnerRenderer->subscriberCountText->simpleText);
                        }
                        if (!empty($info->attributedDescription->content)) {
                            $description = $info->attributedDescription->content;
                        }
                        if (!empty($info->metadataRowContainer->metadataRowContainerRenderer->rows)) {
                            $lists = $info->metadataRowContainer->metadataRowContainerRenderer->rows;
                            foreach ($lists as $list) {
                                if (!empty($list->metadataRowRenderer->title->simpleText)) {
                                    if (strtolower($list->metadataRowRenderer->title->simpleText) == "song") {
                                        if (!empty($list->metadataRowRenderer->contents[0]->runs[0]->text)) {
                                            $songnames[] = $list->metadataRowRenderer->contents[0]->runs[0]->text;
                                            $countSong++;
                                        } else if (!empty($list->metadataRowRenderer->contents[0]->simpleText)) {
                                            $songnames[] = $list->metadataRowRenderer->contents[0]->simpleText;
                                        }
                                    }
                                    if (strtolower($list->metadataRowRenderer->title->simpleText) == "artist") {
                                        if (!empty($list->metadataRowRenderer->contents[0]->runs[0]->text)) {
                                            $artists[] = $list->metadataRowRenderer->contents[0]->runs[0]->text;
                                        } else if (!empty($list->metadataRowRenderer->contents[0]->simpleText)) {
                                            $artists[] = $list->metadataRowRenderer->contents[0]->simpleText;
                                        }
                                    }
                                    if (strtolower($list->metadataRowRenderer->title->simpleText) == "album") {
                                        if (!empty($list->metadataRowRenderer->contents[0]->runs[0]->text)) {
                                            $albums[] = $list->metadataRowRenderer->contents[0]->runs[0]->text;
                                        } else if (!empty($list->metadataRowRenderer->contents[0]->simpleText)) {
                                            $albums[] = $list->metadataRowRenderer->contents[0]->simpleText;
                                        }
                                    }
                                    if (strtolower($list->metadataRowRenderer->title->simpleText) == "licensed to youtube by") {
                                        if (!empty($list->metadataRowRenderer->contents[0]->runs[0]->text)) {
                                            $licenses[] = $list->metadataRowRenderer->contents[0]->runs[0]->text;
                                        } else if (!empty($list->metadataRowRenderer->contents[0]->simpleText)) {
                                            $licenses[] = $list->metadataRowRenderer->contents[0]->simpleText;
                                        }
                                    }
                                    if (strtolower($list->metadataRowRenderer->title->simpleText) == "writers") {
                                        if (!empty($list->metadataRowRenderer->contents[0]->runs[0]->text)) {
                                            $writers[] = $list->metadataRowRenderer->contents[0]->runs[0]->text;
                                        } else if (!empty($list->metadataRowRenderer->contents[0]->simpleText)) {
                                            $writers[] = $list->metadataRowRenderer->contents[0]->simpleText;
                                        }
                                    }
                                }
                            }
                        }
                    }
                    if (!empty($temp->itemSectionRenderer)) {
                        $info = $temp->itemSectionRenderer;
                        if (!empty($info->contents[0]->commentsEntryPointHeaderRenderer->commentCount->simpleText)) {
                            $comment = Utils::shortNumber2Number($info->contents[0]->commentsEntryPointHeaderRenderer->commentCount->simpleText);
                        }
                    }
                }
            }
            //lấy next video
            if (!empty($datas->contents->twoColumnWatchNextResults->autoplay->autoplay->sets[0]->autoplayVideo->watchEndpoint->videoId)) {
                $nextVideo = $datas->contents->twoColumnWatchNextResults->autoplay->autoplay->sets[0]->autoplayVideo->watchEndpoint->videoId;
            }

            //lấy thông tin claim
            if (!empty($datas->engagementPanels)) {
                foreach ($datas->engagementPanels as $tmp) {
                    if (!empty($tmp->engagementPanelSectionListRenderer->content->structuredDescriptionContentRenderer->items)) {
                        $tmp2s = $tmp->engagementPanelSectionListRenderer->content->structuredDescriptionContentRenderer->items;
                        foreach ($tmp2s as $tmp2) {
                            if (!empty($tmp2->videoDescriptionMusicSectionRenderer->carouselLockups)) {
                                $tmp3s = $tmp2->videoDescriptionMusicSectionRenderer->carouselLockups;
                                if (count($tmp3s) == 1) {
                                    //1 bai hat
                                    if (!empty($tmp3s[0]->carouselLockupRenderer->infoRows)) {
                                        $tmp4s = $tmp3s[0]->carouselLockupRenderer->infoRows;
                                        foreach ($tmp4s as $tmp4) {
                                            if (!empty($tmp4->infoRowRenderer->title->simpleText)) {
                                                $check = $tmp4->infoRowRenderer->title->simpleText;
                                                if (strtolower($check) == 'song') {
                                                    if (!empty($tmp4->infoRowRenderer->defaultMetadata->runs[0]->text)) {
                                                        $songnames[] = $tmp4->infoRowRenderer->defaultMetadata->runs[0]->text;
                                                    } elseif (!empty($tmp4->infoRowRenderer->defaultMetadata->simpleText)) {
                                                        $songnames[] = $tmp4->infoRowRenderer->defaultMetadata->simpleText;
                                                    }
                                                } elseif (strtolower($check) == 'artist') {
                                                    if (!empty($tmp4->infoRowRenderer->defaultMetadata->run[0]->text)) {
                                                        $artists[] = $tmp4->infoRowRenderer->defaultMetadata->run[0]->text;
                                                    } elseif (!empty($tmp4->infoRowRenderer->defaultMetadata->simpleText)) {
                                                        $artists[] = $tmp4->infoRowRenderer->defaultMetadata->simpleText;
                                                    }
                                                } elseif (strtolower($check) == 'album') {
                                                    if (!empty($tmp4->infoRowRenderer->defaultMetadata->simpleText)) {
                                                        $albums[] = $tmp4->infoRowRenderer->defaultMetadata->simpleText;
                                                    }
                                                } elseif (strtolower($check) == 'writers') {
                                                    if (!empty($tmp4->infoRowRenderer->defaultMetadata->simpleText)) {
                                                        $writers[] = $tmp4->infoRowRenderer->defaultMetadata->simpleText;
                                                    }
                                                } elseif (strtolower($check) == 'licenses') {
                                                    if (!empty($tmp4->infoRowRenderer->expandedMetadata->simpleText)) {
                                                        $licenses[] = $tmp4->infoRowRenderer->expandedMetadata->simpleText;
                                                    }
                                                }
                                            }
                                        }
                                    }
                                } else {
                                    //nhieu bai hat
                                    foreach ($tmp3s as $tmp3) {
                                        if (!empty($tmp3->carouselLockupRenderer->videoLockup->compactVideoRenderer->title->runs[0]->text)) {
                                            $songnames[] = $tmp3->carouselLockupRenderer->videoLockup->compactVideoRenderer->title->runs[0]->text;
                                        } elseif (!empty($tmp3->carouselLockupRenderer->videoLockup->compactVideoRenderer->title->simpleText)) {
                                            $songnames[] = $tmp3->carouselLockupRenderer->videoLockup->compactVideoRenderer->title->simpleText;
                                        }
                                        if (!empty($tmp3->carouselLockupRenderer->infoRows)) {
                                            $tmp4s = $tmp3->carouselLockupRenderer->infoRows;
                                            foreach ($tmp4s as $tmp4) {
                                                if (!empty($tmp4->infoRowRenderer->title->simpleText)) {
                                                    $check = $tmp4->infoRowRenderer->title->simpleText;
                                                    if (strtolower($check) == 'artist') {
                                                        if (!empty($tmp4->infoRowRenderer->defaultMetadata->runs[0]->text)) {
                                                            $artists[] = $tmp4->infoRowRenderer->defaultMetadata->runs[0]->text;
                                                        } elseif (!empty($tmp4->infoRowRenderer->defaultMetadata->simpleText)) {
                                                            $artists[] = $tmp4->infoRowRenderer->defaultMetadata->simpleText;
                                                        }
                                                    } elseif (strtolower($check) == 'album') {
                                                        if (!empty($tmp4->infoRowRenderer->defaultMetadata->simpleText)) {
                                                            $albums[] = $tmp4->infoRowRenderer->defaultMetadata->simpleText;
                                                        }
                                                    } elseif (strtolower($check) == 'writers') {
                                                        if (!empty($tmp4->infoRowRenderer->defaultMetadata->simpleText)) {
                                                            $writers[] = $tmp4->infoRowRenderer->defaultMetadata->simpleText;
                                                        }
                                                    } elseif (strtolower($check) == 'licenses') {
                                                        if (!empty($tmp4->infoRowRenderer->expandedMetadata->simpleText)) {
                                                            $licenses[] = $tmp4->infoRowRenderer->expandedMetadata->simpleText;
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        } else {
            error_log("getVideoInfoHtmlDesktop $videoId not matches");
        }
        if ($title != "") {
            $status = 1;
        }
        return array("status" => $status, "video_id" => $videoId, "title" => $title, "length" => $videoLength,
            "like" => $like, "dislike" => $dislike, "view" => $views,
            "publish_date" => $publishDate, "channelId" => $channelId, "channelName" => $channelName,
            "song_name" => json_encode($songnames), "artists" => json_encode($artists), "album" => json_encode($albums),
            "license" => json_encode($licenses), "writers" => json_encode($writers),
            "comment" => $comment, "countSong" => $countSong, "next_video" => $nextVideo, "channel_sub" => $channel_sub,
            "description" => $description);
    }

}
