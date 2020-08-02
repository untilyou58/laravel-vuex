<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Messages extends Model
{
    protected $table='messages';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'content', 'type', 'id_line',
        self::CREATED_AT, self::UPDATED_AT,
    ];

    /**
     * The attributes that should be visible for arrays.
     *
     * @var array
     */
    protected $visible = [
        'id', 'content', 'type', 'id_line',
        self::CREATED_AT, self::UPDATED_AT,
    ];
}
