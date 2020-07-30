<?php

namespace App\Models;

class Conversations extends Model
{
    protected $table='conversations';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'type',
        self::CREATED_AT, self::UPDATED_AT,
    ];

    /**
     * The attributes that should be visible for arrays.
     *
     * @var array
     */
    protected $visible = [
        'id', 'type',
        self::CREATED_AT, self::UPDATED_AT,
    ];
}
