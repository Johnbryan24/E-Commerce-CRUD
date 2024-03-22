<?php

namespace App\Http\Controllers;

use App\Models\Products;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
    public function adding(Request $request){

        $items = new Products();
        $items ->name = $request->name;
        $items ->description = $request->description;
        $items ->price = $request->price;
        $items ->quantity = $request->quantity;

        $items->save();

        return response()->json('Added Successfully');

    }

    public function edit(Request $request,){

        $items = Products::findorfail($request->id);
        $items ->name = $request->name;
        $items ->description = $request->description;
        $items ->price = $request->price;
        $items ->quantity = $request->quantity;

        $items->save();

        return response()->json('Update Successfully');

    }
    public function delete(Request $request){

        $items = Products::findorfail($request->id)->delete();
        

        return response()->json('Delete Successfully');

    }
    public function getData(Request $request){

        $items = Products::all();
        

        return response()->json($items);

    }
    
    public function search(Request $request)
    {
        $query = $request->input('q');
        $products = Products::where('name', 'like', "%$query%")->get();
        return response()->json($products);
    }
}
