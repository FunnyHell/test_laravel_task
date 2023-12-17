<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Event extends Model
{
    use HasFactory;

    /**
     *
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'text',
        'creator_id',
    ];

    /**
     *
     *
     * @var array
     */
    protected $dates = [
        'creation_date',
    ];

    /**
     *
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    /**
     *
     */
    public function participants()
    {
        return $this->belongsToMany(User::class, 'event_user', 'event_id', 'user_id');
    }
}
