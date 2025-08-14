<?php

namespace Modules\Availability\app\Entities;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenant;
use Modules\Teams\app\Entities\Team;

class TeamAvailability extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'team_id',
        'day_of_week', // 0 = Sunday, 1 = Monday ...
        'start_time',  // e.g., "09:00"
        'end_time',    // e.g., "17:00"
        'tenant_id',
    ];

    public function team()
    {
        return $this->belongsTo(Team::class);
    }
}
