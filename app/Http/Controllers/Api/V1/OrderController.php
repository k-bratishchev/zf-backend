<?php

namespace App\Http\Controllers\Api\V1;

use App\Filters\V1\OrdersFilter;
use App\Http\Controllers\Controller;
use App\Http\Resources\V1\OrderCollection;
use App\Http\Resources\V1\OrderResource;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class OrderController extends Controller
{

    public function __construct(
        private readonly OrdersFilter $filter,
        private readonly Order $model,
    ) {

    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = $this->model->query();
        $this->filter->transformQuery($query, $request);

        $user = auth()->user();
        if (!$user->isAdmin()) {
            $query->where('user_id', $user->id);
        }

        return new OrderCollection($query->paginate(
            perPage: $request->query('per_page', 10),
        )->appends($request->query()));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {

    }

    /**
     * Store a newly created resource in storage.
     * TODO: порефачить, вынести часть логики из кортроллера в сервисный слой
     */
    public function store(Request $request)
    {
        $orderedProducts = $request->input('products');
        $products = Product::whereIn('id', array_keys($orderedProducts))->get();

        $order = new Order([
            'user_id' => auth()->user()->id,
        ]);
        $order->save();

        $productsData = [];
        $total_price = 0;
        foreach ($products as $product) {
            $quantity = $orderedProducts[$product->id];
            $productsData[] = [
                'product_id' => $product->id,
                'unit_price' => $product->price,
                'quantity' => $quantity,
            ];

            $total_price += $product->price * $quantity;
        }

        $order->products()->saveMany($products, $productsData);
        $order->total_price = $total_price;
        $order->save();

        return $order;
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        $order->load('products');

        return new OrderResource($order);
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
    public function destroy(Order $order)
    {
        Gate::allowIf(fn ($user) => $user->id === $order->user_id || $user->isAdmin());

        $order->delete();

        return response()->noContent();
    }
}
