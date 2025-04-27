<?php

namespace App\Http\Controllers;

use App\Models\ecommerce;
use Illuminate\Http\Request;

class EcommerceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('pages.index', [
            "tittle" => "E-Commerce"
       ]);
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
    public function show(ecommerce $ecommerce)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ecommerce $ecommerce)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ecommerce $ecommerce)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ecommerce $ecommerce)
    {
        //
    }
}
