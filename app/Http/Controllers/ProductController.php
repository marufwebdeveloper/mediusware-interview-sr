<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ProductVariantPrice;
use App\Models\Variant;
use Illuminate\Http\Request;
use DB;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $data = array();
        $data['products'] = $this->get_products($request)->toArray();
        $data['variants'] = $this->pdt_variants();
        $data['product_variants'] = array_reduce(
            $this->get_variant_details(array_column($data['products'], 'p_id')),
            function($d,$r){
                $d[$r->product_id][] = $r;
                return $d;
            },
        []);
        $data['inputs'] = $request->all();
        return view('products.index',$data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function create()
    {
        $variants = Variant::all();
        return view('products.create', compact('variants'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        
    }


    /**
     * Display the specified resource.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function show($product)
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {
        $variants = Variant::all();
        return view('products.edit', compact('variants'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        //
    }

    public function get_products($data)
    {
        

        $products = DB::table('products as p');        
        /*->leftJoin('product_variant_prices as pvp','p.id','pvp.product_id')
        ->leftJoin('product_variants as pv1','pv1.id','pvp.product_variant_one')
        ->leftJoin('product_variants as pv2','pv2.id','pvp.product_variant_two')
        ->leftJoin('product_variants as pv3','pv3.id','pvp.product_variant_three');
        */
        
        if($data['title']){
            $products->whereRaw("LOWER(REPLACE(p.title, ' ', '')) like '%".strtolower(str_replace(' ','', $data['title']))."%' ");
        }
        if($data['date']){
            $products->whereDate("p.created_at",$data['date']);
        }
        if( ($data['price_from'] && $data['price_to']) ||  $data['variant']){
            $products->whereIn('p.id', function($query) use ($data){
                $query->select('product_id')
                ->from('product_variant_prices as pvp');
                if($data['price_from'] && $data['price_to']){
                    $query->whereBetween("price",[$data['price_from'],$data['price_to']]);
                }
                if($data['variant']){
                    if(count($vid = explode('-', $data['variant']))==3){
                        $query->where('pvp.product_variant_one',$vid[0]?:NULL);
                        $query->where('pvp.product_variant_two',$vid[1]?:NULL);
                        $query->where('pvp.product_variant_three',$vid[2]?:NULL);
                    }                    
                } 
            });
        }
           

        $products->orderBy('p.id','desc')
        ->select(
            'p.id as p_id',
            'p.title',
            'p.description'

        );
        return $products->get();

    }

    private function get_variant_details($pdt_ids){
        return DB::table('product_variant_prices as pvp2')
        ->leftJoin('product_variants as pv1','pv1.id','pvp2.product_variant_one')
        ->leftJoin('product_variants as pv2','pv2.id','pvp2.product_variant_two')
        ->leftJoin('product_variants as pv3','pv3.id','pvp2.product_variant_three')
        ->whereIn('pvp2.product_id',$pdt_ids)
        ->select('pvp2.product_id',"pv1.variant as pv1n","pv2.variant as pv2n","pv3.variant as pv3n",'price','stock')
        ->get()->toArray();
    }

    private function query_params($data,$param){
        $fields = explode('|', $param);
        $vals = [];
        foreach ($fields as $v) {
            $vals[]=trim($data[$v]??'');
        }
        return $vals;
    }

    private function pdt_variants(){
        return DB::table('product_variant_prices as pvp')
        ->leftJoin('product_variants as pv1','pv1.id','pvp.product_variant_one')
        ->leftJoin('product_variants as pv2','pv2.id','pvp.product_variant_two')
        ->leftJoin('product_variants as pv3','pv3.id','pvp.product_variant_three')
        ->select('pv1.id as pv1id','pv1.variant as pv1n','pv2.id as pv2id','pv2.variant as pv2n','pv3.id as pv3id','pv3.variant as pv3n')        
        ->get();
    }

    
}

function p($d){
    echo "<pre>";
    print_r($d);
    echo "</pre>";
}
