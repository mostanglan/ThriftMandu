<?php

namespace App\Http\Controllers;

use GuzzleHttp\Promise\Create;
use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function home() {
        return view('product.home');
    }

    public function createProduct() {
        return view('product.create');
    }

    public function storeProduct(Request $request) {
        $request->validate([
            'name' => 'required',
            'price' => 'required|regex:/^\d+(\.\d{1,2})?$/',
            'purchasedDate' => 'required|date',
            'gender' => 'required',
            'description' => 'required',
            'category' => 'required',
            'productImage' => 'required|mimes:jpeg,png,jpg,gif,svg|max:5048',
            'state' => 'required',
        ]);
        

        if($request->hasFile('productImage')) {
            $file = $request->file('productImage');
            $extension = $file->extension();
            $imageName = Str::random(10, 'alpha_num').".".$extension;
            $filePath = 'productimage/'.$imageName;

            Storage::disk('public')->put($filePath, file_get_contents($file));
        } else {
            return back()->with('fail', 'Product Insertion Failed due to image');
        }

        $product = Product::create([
            'name' => $request->name,
            'price' => $request->price,
            'description' => $request->description,
            'category' => $request->category,
            'gender' => $request->gender,
            'image' => $imageName,
            'state' => $request->state,
            'purchased_at' => $request->purchasedDate,
        ]);

        if($product) {
            return back()->with('success', 'Product Inserted Successfully');
        } else {
            return back()->with('fail', 'Product Insertion Failed');
        }
    }
}
