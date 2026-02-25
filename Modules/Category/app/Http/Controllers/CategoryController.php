<?php

namespace Modules\Category\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Category\Http\Requests\CreateCategoryRequest;
use Modules\Category\Http\Requests\UpdateCategoryRequest;
use Modules\Category\Models\Category;
use Modules\Category\Repositories\CategoryRepository;

class CategoryController extends Controller
{
      private $categoryRepo;

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepo = $categoryRepository;
    }

    public function index()
    {
        $user = Auth::user();

        if (! $user) {
            return response()->json(['message' => __('messages.user.Inaccessibility')], 401);
        }

        return IndexCategoryResource::collection($this->categoryRepo->index());
    }

    public function store(CreateCategoryRequest $request)
    {
        $user = Auth::user();

        if (! $user) {
            return response()->json(['message' => __('messages.user.Inaccessibility')], 401);
        }

        $error = $this->categoryRepo->store($request);

        if ($error === null) {
            return response()->json(['message' => __('messages.category.store.success', ['name' => $request->name])], 201);
        }

        return response()->json(['message' => __('messages.category.store.failed', ['name' => $request->name])], 500);
    }

    public function show(Category $category)
    {
        $user = Auth::user();

        if (! $user) {
            return response()->json(['message' => __('messages.user.Inaccessibility')], 401);
        }

        return new ShowCategoryResource($category);

    }

    public function update(Category $category, UpdateCategoryRequest $request)
    {
        $user = Auth::user();

        if (! $user) {
            return response()->json(['message' => __('messages.user.Inaccessibility')], 401);
        }

        if ((int) $request->parent_id === $category->id) {
            return response()->json(['message' => __('messages.category.update.parent_id.failed')], 400);
        }

        $error = $this->categoryRepo->update($category, $request);
        if ($error === null) {
            return response()->json(['message' => __('messages.category.update.success', ['name' => $category->name])], 200);
        }

        return response()->json(['message' => __('messages.category.update.failed', ['name' => $category->name])], 500);
    }

    public function remove_category_image(Category $category)
    {
        $user = Auth::id();

        if (! $user) {
            return response()->json(['message' => __('messages.user.Inaccessibility')], 401);
        }
        $error = $this->categoryRepo->remove_category_image($category);
        if ($error === null) {
            return response()->json(['message' => __('messages.category.image.delete.success')], 200);
        }

        return response()->json(['message' => __('messages.category.image.delete.failed')], 500);
    }

    public function destroy($category)
    {
        $user = Auth::user();

        if (! $user) {
            return response()->json(['message' => __('messages.user.Inaccessibility')], 401);
        }

        $error = $this->categoryRepo->destroy($category);
        if ($error === null) {
            return response()->json(['message' => __('messages.category.delete.success')], 200);
        }

        return response()->json(['message' => __('messages.category.delete.failed')], 500);
    }
}
