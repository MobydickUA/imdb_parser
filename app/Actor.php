<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Actor extends Model
{
    protected $fillable = [
        'id',
        'name',
        'birth_date',
        'birth_place',
        'photo',
        'bio',
        'profile_url',
    ];
    public $timestamps = [];

    public static function getexistingProfilesUrls()
    {
        $urls = [];
        $tmp = self::select('profile_url')->get()->toArray();

        foreach ($tmp as $item) {
            $urls[] = $item['profile_url'];
        }

        return $urls;
    }

    public function roles()
    {
        return $this->hasMany('App\Role');
    }
}
