<?php

namespace App;

use App\Interfaces\iHTMLProvider;

class SimpleHTMLProvider implements iHTMLProvider
{
    private $domain;

    public function __construct(string $domain)
    {
        $this->domain = $domain;
    }

    public function fetchHtml(string $url)
    {
        return str_replace("\n", "", file_get_contents($this->domain . $url));
    }
}
