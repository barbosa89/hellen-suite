<?php

namespace App\Models;

use App\Models\Guest;
use App\Traits\Hashable;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property string $hash
 */
class Country extends Model
{
    use Hashable;

    /**
     * @var array
     */
    protected $fillable = [
        'name'
    ];

    protected $appends = ['hash'];

    protected $hidden = ['id'];

    public function citizens()
    {
        return $this->hasMany(Guest::class);
    }
}
