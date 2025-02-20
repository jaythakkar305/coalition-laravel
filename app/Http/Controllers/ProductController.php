<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Pagination\LengthAwarePaginator;
use Carbon\Carbon;
use Illuminate\Support\Str;


class ProductController extends Controller
{    
    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): View
    {
        // Fetch the data from the json file
        $json = Storage::json('database.json');
        $products = collect($json)->sortByDesc('created_at');

        // Get current page from request
        $page = $request->get('page', 1);

        // Number of items per page
        $perPage = 10;

        // Slice the collection to get the items for the current page
        $items = $products->slice(($page - 1) * $perPage, $perPage);

        // Create paginator instance
        $paginator = new LengthAwarePaginator(
            $items,
            $products->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        if ($request->ajax()) {
            return view('products.list', ['data' => $paginator]);
        }

        return view('products.create', ['data' => $paginator]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): void
    {

        $validated = $request->validate([
            'product_name' => 'required|max:255',
            'quantity_in_stock' => 'required|integer',
            'price_per_item' => 'required|numeric',
        ]);

        $json = Storage::json('database.json');
        $collection = collect($json);

        $collection->push([
            'id' => (string) Str::uuid(),
            ...$validated,
            'created_at' => Carbon::parse($request->startFrom)->format('d-m-Y H:i:s'),
            'total_value_number' => (float) $validated['quantity_in_stock'] * (float) $validated['price_per_item'],
        ]);
        // store the data to file
        Storage::disk('local')->put('database.json', $collection->toJson());
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'product_name' => 'required|max:255',
            'quantity_in_stock' => 'required|integer',
            'price_per_item' => 'required|numeric',
            'record_id' => 'required|string',
        ]);

        $json = Storage::json('database.json');
        $collection = collect($json);

        $oldProduct = $collection->where('id',$validated['record_id'])->first();
        $newProduct = [
            'id' => $oldProduct['id'],
            'product_name' => $validated['product_name'],
            'quantity_in_stock' => $validated['quantity_in_stock'],
            'price_per_item' => $validated['price_per_item'],
            'total_value_number' => (float) $validated['quantity_in_stock'] * (float) $validated['price_per_item'],
            'created_at' => $oldProduct['created_at'],            
        ];
        
        $filtered = $collection->reject(function ($value,$key) use($oldProduct) {            
            return $value['id'] == $oldProduct['id'];
        });
        $filtered->push($newProduct);
        
        // store the data to file
        Storage::disk('local')->put('database.json', $filtered->toJson());
    }

}
