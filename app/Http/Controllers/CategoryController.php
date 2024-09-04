<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Services\CategoryService;
use Exception;

class CategoryController extends Controller
{
    protected $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->middleware('auth:api', ['except' => ['index', 'show']]);
        $this->categoryService = $categoryService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $categories = Category::all();
            return CategoryResource::collection($categories);
        } catch (Exception $e) {
            return response()->json(['message' => 'Failed to fetch categories: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCategoryRequest $request)
    {
        try {

            $validated = $request->validated();
            $category = $this->categoryService->create($validated);
            return new CategoryResource($category);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], $e->getCode());
            ;
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        try {
            return new CategoryResource($category);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], $e->getCode());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCategoryRequest $request, Category $category)
    {
        try {
            $validated = $request->validated();
            $category = $this->categoryService->update($category, $validated);
            return new CategoryResource($category);
        } catch (Exception $e) {
            return response()->json(['message' => 'Failed to update category: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        try {
            $currentUser = auth()->user();

            if ($currentUser && $currentUser->is_admin === 'admin') {
                $this->categoryService->delete($category);
                return response()->json(null, 204);
            }

            return response()->json([
                'message' => 'Unauthorized: You do not have permission to delete this category.'
            ], 403);
        } catch (Exception $e) {
            return response()->json(['message' => 'Failed to delete category: ' . $e->getMessage()], 500);
        }
    }
}
