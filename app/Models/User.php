<?php

namespace App\Models;

use App\Traits\AcceptsTerms;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

/**
 * @method static Builder where(string $string, $email)
 * @method static User create(array $data)
 * @property int id
 * @property string email
 * @property string first_name
 * @property string last_name
 * @property string account_type
 * @property string status
 * @property Carbon email_verified_at
 * @property Carbon created_at
 * @property Carbon date_of_birth
 * @property Profile profile
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens, AcceptsTerms, HasRoles;

    const PERMISSION_USER_LIST = 'user list';
    const PERMISSION_USER_SHOW = 'user show';
    const PERMISSION_USER_CREATE = 'user create';
    const PERMISSION_USER_UPDATE = 'user edit';
    const PERMISSION_USER_DELETE = 'user delete';

    const PERMISSION_ROLE_LIST = 'role list';
    const PERMISSION_ROLE_SHOW = 'role show';
    const PERMISSION_ROLE_CREATE = 'role create';
    const PERMISSION_ROLE_UPDATE = 'role edit';
    const PERMISSION_ROLE_DELETE = 'role delete';

    const PERMISSION_PAGE_LIST = 'page list';
    const PERMISSION_PAGE_SHOW = 'page show';
    const PERMISSION_PAGE_CREATE = 'page create';
    const PERMISSION_PAGE_UPDATE = 'page edit';
    const PERMISSION_PAGE_DELETE = 'page delete';

    const PERMISSION_POST_LIST = 'post list';
    const PERMISSION_POST_SHOW = 'post show';
    const PERMISSION_POST_CREATE = 'post create';
    const PERMISSION_POST_UPDATE = 'post edit';
    const PERMISSION_POST_DELETE = 'post delete';

    const PERMISSION_MENU_LIST = 'menu list';
    const PERMISSION_MENU_CREATE = 'menu create';
    const PERMISSION_MENU_UPDATE = 'menu edit';
    const PERMISSION_MENU_DELETE = 'menu delete';

    const PERMISSION_CATEGORY_LIST = 'category list';
    const PERMISSION_CATEGORY_CREATE = 'category create';
    const PERMISSION_CATEGORY_UPDATE = 'category edit';
    const PERMISSION_CATEGORY_DELETE = 'category delete';

    const PERMISSION_SETTING_LIST = 'setting list';
    const PERMISSION_SETTING_CREATE = 'setting create';
    const PERMISSION_SETTING_UPDATE = 'setting edit';
    const PERMISSION_SETTING_DELETE = 'setting delete';

    /**
     * Account types
     */
    const ACCOUNT_TYPE_CLIENT = 'client';
    const ACCOUNT_TYPE_PROVIDER = 'provider';

    /**
     * User roles
     */
    const ROLE_SUPER_ADMIN = 'super-admin';
    const ROLE_ADMIN = 'admin';
    const ROLE_USER = 'user';

    /**
     * User statuses
     */
    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';

    /**
     * List of statuses
     * @var string[]
     */
    public static array $statuses = [
        self::STATUS_INACTIVE,
        self::STATUS_ACTIVE
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'date_of_birth',
        'account_type'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'date_of_birth' => 'date'
    ];

    /**
     * These attributes should be cast to dates
     * @var string[]
     */
    protected $dates = [
        'created_at'
    ];

    /**
     * The user's profile details
     * @return HasOne
     */
    public function profile(): HasOne
    {
        return $this->hasOne(Profile::class);
    }

    /**
     * Find user by email or return an error
     * @param string $email
     * @return User
     * @throws ModelNotFoundException
     */
    public static function findByEmail(string $email): User
    {
        return self::where('email', $email)->firstOrFail();
    }
}
