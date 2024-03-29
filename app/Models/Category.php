<?php

namespace App\Models;

use Illuminate\Support\Str;
use Laravel\Scout\Searchable;
use Illuminate\Database\Eloquent\Model;
use App\Http\Resources\CategoryResource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory, Searchable;

    protected $fillable = [
        'status_id',
        'name',
        'slug',
        'parent_id',
    ];

    protected $casts = [
        'status_id' => 'integer',
        'parent_id' => 'integer',
    ];

    public $transformer = CategoryResource::class;

    const CATEGORY_IMAGE = 'category image';

    public function toSearchableArray()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
        ];
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id', 'id')
            ->with('children');
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function image()
    {
        return $this->morphOne(Resource::class, 'obtainable')
            ->where('type_resource', self::CATEGORY_IMAGE);
    }

    public function scopeByRole(Builder $query)
    {
        $user = auth('sanctum')->user();

        if ($user && $user->hasRole(Role::ADMIN)) {
            return $query;
        }

        $query->whereHas('status', function ($query) {
            return $query->where('name', Status::ENABLED);
        });
    }

    public function scopeIncludeHasChildren(Builder $query, bool $includeHasChildren)
    {
        if ($includeHasChildren) {
            $query->whereNull('parent_id');
        }
    }

    public function scopeWithEagerLoading(?Builder $query, array $includes, string $type = 'with')
    {
        // $user = auth('sanctum')->user();
        $typeBuilder = $type === 'with' ? $query : $this;

        if (in_array('status', $includes)) {
            $typeBuilder->$type(['status']);
        }

        if (in_array('image', $includes)) {
            $typeBuilder->$type('image');
        }

        if (in_array('children', $includes)) {
            $typeBuilder->$type('children');
        }

        return $typeBuilder;
    }

    public function validByRole()
    {
        $user = auth('sanctum')->user();

        if ($this->status->name === Status::DISABLED) {
            if ($user && $user->hasRole(Role::ADMIN)) {
                return true;
            } else {
                return false;
            }
        } else {
            return true;
        }
    }

    public function setCreate($attributes)
    {
        $data['name'] = $attributes['name'];
        $data['slug'] = Str::slug($data['name'], '-');
        $data['parent_id'] = $attributes['parent_id'];
        $data['image'] = $attributes['image'];
        $data['status_id'] = Status::enabled()->value('id');

        return $data;
    }

    public function setUpdate($attributes)
    {
        !isset($attributes['name']) ?: $data['name'] = $attributes['name'];
        !isset($attributes['name']) ?: $data['slug'] = Str::slug($attributes['name'], '-');
        !isset($attributes['parent_id']) ?: $data['parent_id'] = $attributes['parent_id'];
        !isset($attributes['image']) ?: $data['image'] = $attributes['image'];

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
