<?php

namespace App\Http\Controllers\Api\V1;

use App\Filters\V1\ProductsFilter;
use App\Http\Controllers\Controller;
use App\Http\Resources\V1\ProductCollection;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct(
        private readonly ProductsFilter $filter,
        private readonly Product $model,
    ) {

    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = $this->model->query();
        $this->filter->transformQuery($query, $request);

        return new ProductCollection($query->paginate(
            perPage: $request->query('per_page', 10),
        )->appends($request->query()));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
