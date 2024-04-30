<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\HasDatabaseNotifications;
use Illuminate\Notifications\Notifiable;
use Laravel\Cashier\Billable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable,Billable;

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
        'average_rating', 'resale_license_path', 'photo_of_id_path'
    ];

//    public function getImagePathAttribute()
//    {
//        return asset($this->image);
//    }

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
        return round($this->receivedRatings()->avg('rating'),1);
    }

    public function getResaleLicensePathAttribute()
    {
        return asset('storage/'.$this->resale_license);
    }

    public function getPhotoOfIdPathAttribute()
    {
        return asset('storage/'.$this->photo_of_id);
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

    public function inventories(){
        return $this->hasMany(Product::class);
    }

    public function store(){
        return $this->hasMany(Product::class)->where('is_published', 1);
    }
}
