<?php

namespace App\Models;

use App\Traits\HasFiles;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string photo
 * @property string description
 * @property string phone_number
 * @property string gender
 */
class Profile extends Model
{
    use HasFactory, HasFiles;

    /**
     * Gender options
     */
    const GENDER_MALE = 'male';
    const GENDER_FEMALE = 'female';
    const GENDER_NONE = 'i prefer not to say';

    /**
     * Gender options
     * @var string[]
     */
    public static array $genders = [
        self::GENDER_MALE,
        self::GENDER_FEMALE,
        self::GENDER_NONE
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'photo',
        'description',
        'phone_number',
        'gender',
    ];

    /**
     * The profile that owns the user
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function photoUrl(): ?string
    {
        return $this->fileUrl($this->photo);
    }
}
