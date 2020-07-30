<?php

namespace App\Models;

class UserConversation extends Model
{
    protected $table='user_conversation';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'conversation_id',
        self::CREATED_AT, self::UPDATED_AT,
    ];

    /**
     * The attributes that should be visible for arrays.
     *
     * @var array
     */
    protected $visible = [
        'user_id', 'conversation_id',
        self::CREATED_AT, self::UPDATED_AT,
    ];
}
