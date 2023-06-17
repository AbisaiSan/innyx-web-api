<?php

namespace App\Models;

use Attribute;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\Category;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'price'];

    protected $with = ['categories'];

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }

    //Possivelmenrte excluir isso
    // protected $appends = ['price_float'];

    // public function priceFloat(): Attribute 
    // {
    //     return new Attribute(
    //         get: fn($price) => $this->attributes['price'] / 100
    //     );
    // }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('d/m/Y H:i');
    }
}
