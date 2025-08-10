<?php

namespace App\Transformer;

use Illuminate\Support\Collection;

/**
 * 基礎轉換器抽象類
 * 
 * 提供通用的集合轉換方法，消除重複代碼
 * 子類只需實現 transform() 方法即可
 */
abstract class BaseTransformer
{
    /**
     * 轉換單個資源 - 由子類實現
     * 
     * @param mixed $resource
     * @return array
     */
    abstract public function transform($resource): array;

    /**
     * 轉換集合資源 - 通用實現
     */
    public function transformCollection(Collection $items): array
    {
        return $items->map(fn($item) => $this->transform($item))->toArray();
    }
}