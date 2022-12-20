@extends('layouts.app')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Product</h1>
    </div>
    <div id="app">
    	@php
        $url = json_encode([
        	'base_url'=>url('/'),
        	'asset_url'=>asset('')
        ]);
        @endphp
        <edit-product 
        :urls="{{$url}}" 
        :variants="{{ $variants }}"
        :product_data="{{$product->toJson()}}"
        :product_images="{{$images->toJson()}}"
        :product_variants="{{$product_variants->toJson()}}"
        :pdt_variant_prices="{{$product_variant_prices->toJson()}}"        
        >Loading</edit-product>
    </div>
@endsection
