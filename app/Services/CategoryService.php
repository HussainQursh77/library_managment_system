<?php

namespace App\Services;

use App\Models\Category;

class CategoryService
{
    /**
     * Create a new category.
     *
     * @param array $data
     * @return Category
     */
    public function create(array $data): Category
    {
        return Category::create($data);
    }

    /**
     * Update an existing category.
     *
     * @param Category $category
     * @param array $data
     * @return Category
     */
    public function update(Category $category, array $data): Category
    {
        $category->update($data);
        return $category;
    }

    /**
     * Delete a category.
     *
     * @param Category $category
     * @return void
     */
    public function delete(Category $category): void
    {
        $category->delete();
    }
}
