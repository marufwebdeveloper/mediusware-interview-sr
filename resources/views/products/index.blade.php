@extends('layouts.app')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Products</h1>
    </div>  
    <div class="card">
        <form action="{{route('product.index')}}" method="get" class="card-header">

            <div class="form-row justify-content-between">
                <div class="col-md-2">
                    <input type="text" name="title" value="{{$inputs['title']??''}}" placeholder="Product Title" class="form-control">
                </div>
                <div class="col-md-2">
                    <select multiple="" class="form-control" name="variant[]">
                    @foreach($hierarchy_variants as $vi)
                        <optgroup label="{{$vi['parent'][1]}}">
                            @foreach(array_unique(array_map( "strtolower", $vi['data'] )) as $_variant)
                                @php
                                    $vv = $vi['parent'][0].'||'.strtolower(str_replace(' ','',$_variant));
                                @endphp
                                <option value="{{$vv}}" {{in_array($vv,($inputs['variant']??[]))?"selected":''}}>{{$_variant}}
                                </option>
                            @endforeach
                        </optgroup>
                    @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Price Range</span>
                        </div>
                        <input type="text" name="price_from" value="{{$inputs['price_from']??''}}" aria-label="First name" placeholder="From" class="form-control">
                        <input type="text" name="price_to" value="{{$inputs['price_to']??''}}" aria-label="Last name" placeholder="To" class="form-control">
                    </div>
                </div>
                <div class="col-md-2">
                    <input type="date" name="date" value="{{$inputs['date']??''}}" placeholder="Date" class="form-control">
                </div>
                <div class="col-md-1">

                    <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i></button>
                    <a href="{{route('product.index')}}" class="btn btn-success" title="Reset">R</a>
                </div>
            </div>
        </form>

        <div class="card-body">
            <div class="table-response">
                <table class="table">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Title</th>
                        <th>Description</th>
                        <th>Variant</th>
                        <th width="150px">Action</th>
                    </tr>
                    </thead>

                    <tbody>
                    @if(count($products))
                        @foreach($products as $k=>$v)
                        <tr>
                            <td>{{$k+1}}</td>
                            <td>{{$v->title}}</td>
                            <td style="max-width: 100px">{{substr($v->description,0,200)}}...</td>                            
                            <td>
                                @foreach($product_variants[$v->p_id]??[] as $pv)
                                <dl class="row mb-0 h-auto" style="height: 80px; overflow: hidden" data-variant="td-variant">
                                    <dt class="col-sm-3 pb-0">
                                        {{ implode('/',array_filter([$pv->pv1n,$pv->pv2n,$pv->pv3n]))}}
                                    </dt>
                                    <dd class="col-sm-9">
                                        <dl class="row mb-0">
                                            <dt class="col-sm-4 pb-0">Price : {{ number_format($pv->price,2) }}</dt>
                                            <dd class="col-sm-8 pb-0">InStock : {{ number_format($pv->stock,2) }}</dd>
                                        </dl>
                                    </dd>
                                </dl>
                                @endforeach
                                <button onclick="$('[data-variant=\'td-variant\']').toggleClass('h-auto')" class="btn btn-sm btn-link">Show more</button>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('product.edit', $v->p_id) }}" class="btn btn-success">Edit</a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    @else
                    <tr>
                        <td colspan="6" style="text-align: center;">No Data Found</td>
                    </tr>
                    @endif

                    </tbody>

                </table>
            </div>

        </div>

        <div class="card-footer">
            <div class="row justify-content-between">
                <div class="col-md-6">
                    <p>Showing 1 to 10 out of 100</p>
                </div>
                <div class="col-md-2">

                </div>
            </div>
        </div>
    </div>

@endsection
