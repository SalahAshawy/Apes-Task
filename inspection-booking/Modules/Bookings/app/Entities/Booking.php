<?php

namespace Modules\Bookings\app\Entities;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenant;
use Modules\Teams\app\Entities\Team;
use Modules\Users\Entities\User;

class Booking extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'user_id',
        'team_id',
        'start_time',
        'end_time',
        'date',
        'tenant_id',
    ];

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
