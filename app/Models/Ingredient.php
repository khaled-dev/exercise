<?php

namespace App\Models;

use App\Models\Concerns\IngredientStockUpdateStatementBuilder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Ingredient extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['our_of_stock_notification'];

    public function getStockBelowSafePointAttribute(): bool
    {
        return $this->stock < ($this->maximum_stock / 2);
    }

    public static function updateStock(Collection $weights): bool
    {
        $statementBuilder = new IngredientStockUpdateStatementBuilder();

        $weights->each(function ($ingredientWeight) use ($statementBuilder) {
            $statementBuilder->deductIngredientWeight($ingredientWeight->id, $ingredientWeight->weights);
        });

        return DB::statement(
            DB::raw($statementBuilder->build())
        );
    }
}
