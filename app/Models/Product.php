<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class Product extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'price',
    ];

    public function getConvertedPriceAttribute(): string
    {
        return $this->price . ' EGP';
    }

    public function recipes(): HasMany
    {
        return $this->hasMany(Recipe::class);
    }

    public function scopeIngredientQuantity(Builder $q, array $productIds)
    {
        return $q->selectRaw('recipes.ingredient_id as id, ingredients.name as name, sum(recipes.weight) as weights')
            ->leftJoin('recipes', 'recipes.product_id', '=','products.id')
            ->leftJoin('ingredients', 'ingredients.id', '=','recipes.ingredient_id')
            ->whereIn('products.id', $productIds)
            ->groupBy('recipes.ingredient_id');
    }

    public function ingredients(): BelongsToMany
    {
        return $this->belongsToMany(Ingredient::class)
            ->using(Recipe::class)
            ->withPivot('weight');
    }

    public function orders(): BelongsToMany
    {
        return $this->belongsToMany(Order::class);
    }
}
