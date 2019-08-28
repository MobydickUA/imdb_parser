<?php

namespace App;

use App\Interfaces\iHTMLProvider;

use Illuminate\Support\Facades\Log;


class ParseHelper
{
    private const PROFILE_PER_PAGE = 50;
    private $HTMLProvider;
    private $profileLinks = [];
    private $currentPageNumber = 1;

    public function __construct(iHTMLProvider $htmlProvider)
    {
        $this->HTMLProvider = $htmlProvider;
    }

    public function getNextProfileLink()
    {
        $templateUrl = "/search/name/?gender=male,female&start=";

        if (!$this->profileLinks || !count($this->profileLinks)) {
            $offset = ($this->currentPageNumber - 1) * ParseHelper::PROFILE_PER_PAGE + 1;
            $response = $this->HTMLProvider->fetchHTML($templateUrl . $offset);
            $this->profileLinks = $this->getProfileUrls($response);
            $this->currentPageNumber++;
        }

        return array_shift($this->profileLinks);
    }

    public function parseProfile($url)
    {
        $profileData = [];
        $profile = $this->HTMLProvider->fetchHTML($url);
        $profileData['name'] = $this->getProfileName($profile);
        $profileData['photo'] = $this->getProfilePhoto($profile);
        $profileData['birth_date'] = $this->getProfileBirthDate($profile);
        $profileData['birth_place'] = $this->getProfileBirthPlace($profile);
        $profileData['films'] = $this->getProfileFilms($profile);
        $profile = $this->HTMLProvider->fetchHTML($url . "/bio");
        $profileData['bio'] = $this->getProfileBio($profile);
        $profileData['profile_url'] = $url;

        return $profileData;
    }

    private function getNextPageUrl($html)
    {
        $found = preg_match('/href\="(.*)?"\nclass\="lister-page-next next-page"/', $html, $matches);

        return $found ? $matches[1] : null;
    }

    private function getProfileUrls($html)
    {
        $urls = [];
        if (preg_match_all('/href\="(\/name\/nm[\d]{7})?"/m', $html, $matches)) {
            $urls = array_unique($matches[1]);
        }

        return $urls;
    }

    private function getProfileName($html)
    {
        $found = preg_match('/h1 class\="header"\> \<span class=\"itemprop\"\>([^\<]*)\<\/span\>/', $html, $matches);

        return $found ? $matches[1] : null;
    }

    private function getProfilePhoto($html)
    {
        $found = preg_match('/img id\="name-poster"[^\<]*src\="([^\<]*)?"/', $html, $matches);

        return $found ? $matches[1] : null;
    }

    private function getProfileBirthDate($html)
    {
        $found = preg_match('/\<time datetime\="([^"]*)?"/', $html, $matches);

        return $found ? $matches[1] : null;
    }

    private function getProfileBirthPlace($html)
    {
        $found = preg_match('/birth_place\=[^>]*>([^<]*)?\</U', $html, $matches);

        return $found ? $matches[1] : null;
    }

    private function getProfileBio($html)
    {
        $found = preg_match('/div class\=\"soda odd\">(.*)?\<\/p>/U', $html, $matches);

        return $found ? trim(strip_tags($matches[1])) : null;
    }

    private function getProfileFilms($html)
    {
        $films = [];
        $filmsFound = preg_match_all('/class\="knownfor-ellipsis"[^\<]*\>([^\<]*)?\<\//U', $html, $matches);

        if ($filmsFound) {
            for ($i = 0; $i < count($matches[1]); $i += 3) {
                $films[] = [
                    'film' => $matches[1][$i],
                    'role' => $matches[1][$i + 1],
                    'year' => trim($matches[1][$i + 2], "()"),
                ];
            }
        }

        return $films;
    }
}
