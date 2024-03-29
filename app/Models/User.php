<?php

namespace App\Models;

use Laravel\Scout\Searchable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\UserResource;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, Searchable;

    protected $fillable = [
        'name',
        'email',
        'username',
        'password',
        'status_id',
        'email_verified_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'status_id' => 'integer',
        'email_verified_at' => 'datetime',
    ];

    public $transformer = UserResource::class;

    public function toSearchableArray()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'username' => $this->username,
        ];
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function socialNetworks()
    {
        return $this->hasMany(SocialNetwork::class);
    }

    public function scopeWithEagerLoading(?Builder $query, array $includes)
    {
        // $user = auth('sanctum')->user();
        $typeBuilder = $query ?? $this;
        $type = $query ? 'with' : 'load';

        if (in_array('status', $includes)) {
            $typeBuilder->$type(['status']);
        }

        if (in_array('roles', $includes)) {
            $typeBuilder->$type(['roles']);
        }

        if (in_array('social_networks', $includes)) {
            $typeBuilder->$type(['socialNetworks']);
        }

        return $typeBuilder;
    }

    public function isAdmin()
    {
        return $this->hasRole(Role::ADMIN);
    }

    public function setCreate($attributes)
    {
        $data['name'] = $attributes['name'];
        $data['email'] = $attributes['email'];
        $data['username'] = $attributes['username'];
        $data['password'] = Hash::make($attributes['password']);
        $data['status_id'] = Status::enabled()->value('id');

        return $data;
    }

    public function setUpdate($attributes)
    {
        !$attributes['name'] ?: $this->name = $attributes['name'];
        !$attributes['username'] ?: $this->username = $attributes['username'];

        return $this;
    }

    public function setUpdatePassword($attributes)
    {
        $data['password'] = $attributes['password'];
        $data['new_password'] = $attributes['new_password'];

        return $data;
    }

    public function setDelete()
    {
        if ($this->status_id === Status::disabled()->value('id')) {
            $this->status_id = Status::enabled()->value('id');
        } else if ($this->status_id === Status::enabled()->value('id')) {
            $this->status_id = Status::disabled()->value('id');
        }

        return $this;
    }
}
