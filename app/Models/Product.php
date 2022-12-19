<?php

namespace App\Models;

use App\DataTransferObjects\IngredientWeightDTO;
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

    public function scopeRecipeWeights(Builder $q,  array $productsQuantities)
    {
        $productIds = array_column($productsQuantities, 'product_id');

        $q->selectRaw('
                    recipes.ingredient_id as id, ingredients.name as name, '
                    . $this->generateWeightConditions($productsQuantities)
                    . ' as weights'
            )
            ->leftJoin('recipes', 'recipes.product_id', '=','products.id')
            ->leftJoin('ingredients', 'ingredients.id', '=','recipes.ingredient_id')
            ->whereIn('products.id', $productIds)
            ->groupBy('recipes.ingredient_id', 'products.id');
    }

    public function scopeRecipeWeightsDistinct(Builder $q)
    {
        return collect(
            $q->get()->groupBy('id')->map(function ($ingredientWeight) {
                return new IngredientWeightDTO(
                    $ingredientWeight->first()->id,
                    $ingredientWeight->first()->name,
                    $ingredientWeight->sum('weights'),
                );
            })
        );
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

    private function generateWeightConditions(array $productsQuantities): string
    {
        $statement = ' ( case';

        foreach ($productsQuantities as $productQuantity) {
            $statement .= ' WHEN products.id = ' . $productQuantity['product_id'] . ' then sum(recipes.weight) * ' . $productQuantity['quantity'];
        }

        $statement .=  ' end ) ';

        return $statement;
    }
}
