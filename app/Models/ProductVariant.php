<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    protected $fillable = [
        'product_id',
        'attributes',
        'price',
        'sku_variant',
        'stock',
    ];

    protected function casts(): array
    {
        return [
            'attributes' => 'array',
            'price' => 'integer',
            'stock' => 'integer',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIPS
    |--------------------------------------------------------------------------
    */

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    /*
    |--------------------------------------------------------------------------
    | LABEL
    |--------------------------------------------------------------------------
    |
    | Contoh:
    |
    | Warna: Hitam / Ukuran: M / Bahan: Cotton
    |
    |--------------------------------------------------------------------------
    */

    public function label(): string
    {
        $product = $this->product;

        if (!$product) {
            return '-';
        }

        $groups = $product->variant_groups ?? [];

        /*
        | IMPORTANT
        | Jangan gunakan $this->attributes karena itu adalah
        | raw attributes milik Eloquent.
        */
        $attributes = $this->getAttribute('attributes') ?? [];

        if (!is_array($attributes)) {
            return '-';
        }

        /*
        |--------------------------------------------------------------------------
        | ASSOCIATIVE ARRAY
        |--------------------------------------------------------------------------
        */

        if ($this->isAssociativeArray($attributes)) {
            return collect($groups)
                ->map(function ($group) use ($attributes) {
                    $name = $group['name'] ?? 'Variant';

                    $value = $attributes[$name] ?? '-';

                    return $name . ': ' . $value;
                })
                ->implode(' / ');
        }

        /*
        |--------------------------------------------------------------------------
        | INDEXED ARRAY
        |--------------------------------------------------------------------------
        |
        | Contoh:
        |
        | [
        |     "Hitam",
        |     "M",
        |     "Cotton"
        | ]
        |
        |--------------------------------------------------------------------------
        */

        return collect($groups)
            ->values()
            ->map(function ($group, $index) use ($attributes) {
                $name = $group['name'] ?? 'Variant';

                $value = $attributes[$index] ?? '-';

                return $name . ': ' . $value;
            })
            ->implode(' / ');
    }

    /*
    |--------------------------------------------------------------------------
    | EFFECTIVE PRICE
    |--------------------------------------------------------------------------
    */

    public function effectivePrice(): int
    {
        return (int) (
            $this->price
            ?? $this->product?->price
            ?? 0
        );
    }

    /*
    |--------------------------------------------------------------------------
    | GET ATTRIBUTE VALUE BY GROUP
    |--------------------------------------------------------------------------
    */

    public function getAttributeValueByGroup(
        string $groupName
    ): ?string {
        $product = $this->product;

        if (!$product) {
            return null;
        }

        $groups = $product->variant_groups ?? [];

        $attributes = $this->getAttribute('attributes') ?? [];

        if (!is_array($attributes)) {
            return null;
        }

        foreach ($groups as $index => $group) {
            if (($group['name'] ?? '') !== $groupName) {
                continue;
            }

            /*
            | Associative:
            |
            | [
            |     "Warna" => "Hitam"
            | ]
            */

            if (array_key_exists($groupName, $attributes)) {
                return $attributes[$groupName];
            }

            /*
            | Indexed:
            |
            | [
            |     "Hitam",
            |     "M",
            |     "Cotton"
            | ]
            */

            return $attributes[$index] ?? null;
        }

        return null;
    }

    /*
    |--------------------------------------------------------------------------
    | ASSOCIATIVE ARRAY CHECK
    |--------------------------------------------------------------------------
    */

    protected function isAssociativeArray(array $array): bool
    {
        if ($array === []) {
            return false;
        }

        return array_keys($array) !== range(
            0,
            count($array) - 1
        );
    }

    public function images()
    {
        return $this->hasMany(ProductVariantImage::class);
    }
}
