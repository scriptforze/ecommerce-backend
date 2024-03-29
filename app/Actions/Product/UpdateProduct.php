<?php

namespace App\Actions\Product;

use App\Models\Status;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class UpdateProduct
{
    /**
     * Handle the incoming action.
     */
    public function __invoke(array $fields, Product $product)
    {
        DB::beginTransaction();
        try {
            $oldProduct = $product->getOriginal();
            $product->update($fields);

            if ($oldProduct['is_variable'] !== $product->is_variable) {
                $product->productStocks()
                    ->where('status_id', Status::enabled()->value('id'))
                    ->get()
                    ->update([
                        'status_id' => Status::disabled()->value('id')
                    ]);
            }

            if (array_key_exists('images', $fields) && count($fields['images'])) {
                app(SyncProductImages::class)($product, $fields['images']);
            }

            if (array_key_exists('tags', $fields) && count($fields['tags'])) {
                app(SyncProductTags::class)($product, $fields['tags']);
            }

            if (
                array_key_exists('product_attribute_options', $fields)
                && count($fields['product_attribute_options'])
            ) {
                app(SyncProductAttributeOptions::class)($product, $fields['product_attribute_options']);
            }

            app(SyncProductOptions::class)($product);
            DB::commit();

            return $product;
        } catch (\Exception $exception) {
            DB::rollBack();
            throw new \Exception($exception->getMessage());
        }
    }
}
