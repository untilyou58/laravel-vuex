<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class Photo extends Model
{
    /** Primary key type */
    protected $keyType = 'string';

    /** JSON attributes is visible */
    protected $visible = [
        'id', 'owner', 'url', 'comments',
        'likes_count', 'liked_by_user',
    ];

    /** JSON attributes include */
    protected $appends = [
        'url', 'likes_count', 'liked_by_user'
    ];

    /** JSON attributes hidden */
    protected $hidden = [
        'user_id', 'filename',
        self::CREATED_AT, self::UPDATED_AT,
    ];

    /** Number of photos per page */
    protected $perPage = 15;

    /** ID length */
    const ID_LENGTH = 12;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        if (! Arr::get($this->attributes, 'id'))
        {
            $this->setId();
        }
    }

    /**
     * Assign a random ID value to the id attribute
     */
    private function setId()
    {
        $this->attributes['id'] = $this->getRandomId();
    }

    /**
     * Create an ID randomly
     * @return string
     */
    private function getRandomId()
    {
        $characters = array_merge(
            range(0, 9), range('a', 'z'),
            range('A', 'Z'), ['-', '_']
        );

        $length = count($characters);

        $id = "";

        for ($i = 0; $i < self::ID_LENGTH; $i++) {
            $id .= $characters[random_int(0, $length - 1)];
        }

        return $id;
    }

    /**
     * Accessor - url
     * @return string
     */
    public function getUrlAttribute()
    {
        return Storage::cloud()->url($this->attributes['filename']);
    }

    /**
     * Accessor - likes_count
     * @return int
     */
    public function getLikesCountAttribute()
    {
        return $this->likes->count();
    }

    /**
     * Accessor - liked_by_user
     * @return boolean
     */
    public function getLikedByUserAttribute()
    {
        if (Auth::guest()) {
            return false;
        }

        return $this->likes->contains(function ($user) {
            return $user->id === Auth::user()->id;
        });
    }

    /**
     * Relationship - users table
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function owner()
    {
        return $this->belongsTo('App\User', 'user_id', 'id', 'users');
    }

    /**
     * Relationship - comments table
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function comments()
    {
        return $this->hasMany('App\Comment')->orderBy('id', 'desc');
    }

    /**
     * Relationship - users table
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function likes()
    {
        return $this->belongsToMany('App\User', 'likes')->withTimestamps();
    }
}
