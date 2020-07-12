<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    /** JSON visible */
    protected $visible = [
        'author', 'content',
    ];

    /**
     * Relationship - users table
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function author()
    {
        return $this->belongsTo('App\User', 'user_id', 'id', 'users');
    }
}
