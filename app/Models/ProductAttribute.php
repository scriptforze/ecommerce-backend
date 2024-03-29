<?php

namespace App\Models;

use Laravel\Scout\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Http\Resources\ProductAttributeResource;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductAttribute extends Model
{
    use HasFactory, Searchable;

    protected $fillable = [
        'status_id',
        'name',
        'type',
    ];

    protected $casts = [
        'status_id' => 'integer',
    ];

    public $transformer = ProductAttributeResource::class;

    const BUTTON_TYPE = 'button';
    const SELECT_TYPE = 'selector';
    const COLOR_TYPE = 'color';

    const TYPES = [
        self::BUTTON_TYPE,
        self::SELECT_TYPE,
        self::COLOR_TYPE,
    ];

    public function toSearchableArray()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'type' => $this->type,
        ];
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function productAttributeOptions()
    {
        return $this->hasMany(ProductAttributeOption::class);
    }

    public function scopeWithEagerLoading(?Builder $query, array $includes, string $type = 'with')
    {
        // $user = auth('sanctum')->user();
        $typeBuilder = $type === 'with' ? $query : $this;

        if (in_array('status', $includes)) {
            $typeBuilder->$type(['status']);
        }

        if (in_array('product_attribute_options', $includes)) {
            $typeBuilder->$type([
                'productAttributeOptions' => function ($query) {
                    $query->whereHas('status', function ($query) {
                        $query->where('name', Status::ENABLED);
                    });
                }
            ]);
        }

        return $typeBuilder;
    }

    public function setCreate($attributes)
    {
        $data['name'] = $attributes['name'];
        $data['type'] = $attributes['type'];
        $data['status_id'] = Status::enabled()->value('id');

        return $data;
    }

    public function setUpdate($attributes)
    {
        !$attributes['name'] ?: $this->name = $attributes['name'];
        !$attributes['type'] ?: $this->type = $attributes['type'];

        return $this;
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
