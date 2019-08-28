<?php

namespace App\Http\Controllers;

use App\Role;
use App\Actor;
use App\Config;
use App\ParseHelper;
use App\SimpleHTMLProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ParseController extends Controller
{
    private $domain;

    public function __construct()
    {
        $domain = Config::where(['type' => 'domain'])->first();
        $this->domain = $domain ? $domain->value : 'https://www.imdb.com';
    }

    public function parse()
    {
        $htmlProvider = new SimpleHTMLProvider($this->domain);
        $model = new ParseHelper($htmlProvider);

        $amount = Config::where(['type' => 'profile_amount'])->first();
        $amountOfProfiles = $amount ? $amount->value : 500;

        Log::info("Started");
        $newProfiles = 0;
        while (($newProfiles < $amountOfProfiles) && ($url = $model->getNextProfileLink())) {
            if (!Actor::where(['profile_url' => $url])->exists()) {
                $this->sendRequest($url);
                $newProfiles++;
            } else {
                Log::info("Profile $url already exists in database, skipping...");
            }
        }

        Log::info("Finished - $amountOfProfiles were parsed");
    }

    private function sendRequest($url)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $_SERVER['SERVER_NAME'] . "/api/getProfile");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, ['url' => $url]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 1); 
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
        curl_setopt($ch, CURLOPT_FORBID_REUSE, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1);
        curl_setopt($ch, CURLOPT_DNS_CACHE_TIMEOUT, 10); 
        curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);

        curl_exec($ch);
        curl_close($ch);
    }

    public function parseAndSaveProfile(Request $request) {
        $htmlProvider = new SimpleHTMLProvider($this->domain);
        $model = new ParseHelper($htmlProvider);
        $data = $model->parseProfile($request->input('url'));
        Log::info("Fetched " . $data['name']);

        \DB::transaction(function () use ($data) {
            $actor = new Actor($data);
            try {
                $actor->save();
                Role::insert(array_map(function ($item) use ($actor) {
                    $item['actor_id'] = $actor->id;
                    return $item;
                }, $data['films']));
            } catch (Exception $e) {
                Log::error($e->getMessage());
            }
        });
    }
}
