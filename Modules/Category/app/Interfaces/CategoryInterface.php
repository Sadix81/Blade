<?php

namespace Modules\Category\Interfaces;

interface CategoryInterface
{
    public function index();

    public function store($request);

    public function update($category, $request);

    public function remove_category_image($category);

    public function destroy($category);
}
