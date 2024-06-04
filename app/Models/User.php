<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Services\FileUploadService;
use App\Traits\ModelAttributeTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\HasDatabaseNotifications;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Laravel\Cashier\Billable;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, Billable, ModelAttributeTrait, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'subscription_is_active',
        'is_approved_by_admin',
        'approved_by',
        'business_name',
        'phone',
        'business_address',
        'online_store_url',
        'ein',
        'resale_license',
        'photo_of_id',
        'stripe_customer_id',
        'is_super_swapper',
        'photo_of_id',
        'stripe_customer_id',
        'about_me',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    protected $appends = [
        'average_rating', 'resale_license_info', 'photo_of_id_info'
    ];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function paymentMethods(): HasMany
    {
        return $this->hasMany(PaymentMethods::class);
    }

    public function activePaymentMethod(): HasOne
    {
        return $this->hasOne(PaymentMethods::class)->where('is_active', 1);
    }

    public function givenRatings(): HasMany
    {
        return $this->hasMany(Rating::class, 'user_id');
    }

    public function receivedRatings(): HasMany
    {
        return $this->hasMany(Rating::class, 'rated_id');
    }

    public function getAverageRatingAttribute()
    {
        return round($this->receivedRatings()->avg('rating'), 1);
    }

    public function getResaleLicenseInfoAttribute()
    {
        if ($this->resale_license && file_exists(public_path('storage/' . $this->resale_license))) {
            return [
                'size' => FileUploadService::formatSizeUnits(File::size(public_path('storage/' . $this->resale_license))),
                'extension' => File::extension($this->resale_license),
                'basename' => File::basename($this->resale_license),
                'path' => asset('storage/' . $this->resale_license),
                'exist' => file_exists(public_path('storage/' . $this->resale_license))
            ];
        } else {
            return $this->nullFileInfo();
        }

    }


    public function getPhotoOfIdInfoAttribute()
    {
        if ($this->photo_of_id && file_exists(public_path('storage/' . $this->photo_of_id))) {
            return [
                'size' => FileUploadService::formatSizeUnits(File::size(public_path('storage/' . $this->photo_of_id))),
                'extension' => File::extension($this->photo_of_id),
                'basename' => File::basename($this->photo_of_id),
                'path' => asset('storage/' . $this->photo_of_id),
                'exist' => file_exists(public_path('storage/' . $this->photo_of_id))
            ];
        } else {
            return $this->nullFileInfo();
        }
    }

    protected function nullFileInfo()
    {
        return [
            'size' => null,
            'extension' => null,
            'basename' => null,
            'path' => null,
            'exist' => false

        ];
    }

    public function notifications(): BelongsToMany
    {
        return $this->belongsToMany(Notification::class);

    }

    public function unreadNotifications()
    {
        return $this->notifications()->whereNull('read_at');
    }

    public function readNotifications()
    {
        return $this->notifications()->whereNotNull('read_at');
    }

    public function image()
    {
        return $this->morphOne(Image::class, 'imageable');
    }

    public function activeSubscriptions(): HasOne
    {
        return $this->hasOne(Subscription::class)->where('status', 'active');
    }

    public function inventories()
    {
        return $this->hasMany(Product::class);
    }

    public function store()
    {
        return $this->hasMany(Product::class)->where('is_publish', 1);
    }

    public function billings(): HasMany
    {
        return $this->hasMany(Billing::class);
    }

//     $role = Role::create(['name' => 'admin']);
//     $permission = Permission::create(['name' => 'user.index']);
//     $role->givePermissionTo($permission);
//     $user = User::find(1);
//     $user->assignRole('admin');
}
