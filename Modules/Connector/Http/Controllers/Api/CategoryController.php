<?php

namespace Modules\Connector\Http\Controllers\Api;

use App\Category;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Connector\Transformers\CommonResource;
use Modules\Sitemanager\Entities\SlideMedia;

/**
 * @group Taxonomy management
 * @authenticated
 *
 * APIs for managing taxonomies
 */
class CategoryController extends ApiController
{
    /**
     * List taxonomy
     *
     * @queryParam type Type of taxonomy (product, device, hrm_department)
     *
     * @response {
            "data": [
                {
                    "id": 1,
                    "name": "Men's",
                    "business_id": 1,
                    "short_code": null,
                    "parent_id": 0,
                    "created_by": 1,
                    "category_type": "product",
                    "description": null,
                    "slug": null,
                    "woocommerce_cat_id": null,
                    "deleted_at": null,
                    "created_at": "2018-01-03 21:06:34",
                    "updated_at": "2018-01-03 21:06:34",
                    "sub_categories": [
                        {
                            "id": 4,
                            "name": "Jeans",
                            "business_id": 1,
                            "short_code": null,
                            "parent_id": 1,
                            "created_by": 1,
                            "category_type": "product",
                            "description": null,
                            "slug": null,
                            "woocommerce_cat_id": null,
                            "deleted_at": null,
                            "created_at": "2018-01-03 21:07:34",
                            "updated_at": "2018-01-03 21:07:34"
                        },
                        {
                            "id": 5,
                            "name": "Shirts",
                            "business_id": 1,
                            "short_code": null,
                            "parent_id": 1,
                            "created_by": 1,
                            "category_type": "product",
                            "description": null,
                            "slug": null,
                            "woocommerce_cat_id": null,
                            "deleted_at": null,
                            "created_at": "2018-01-03 21:08:18",
                            "updated_at": "2018-01-03 21:08:18"
                        }
                    ]
                },
                {
                    "id": 21,
                    "name": "Food & Grocery",
                    "business_id": 1,
                    "short_code": null,
                    "parent_id": 0,
                    "created_by": 1,
                    "category_type": "product",
                    "description": null,
                    "slug": null,
                    "woocommerce_cat_id": null,
                    "deleted_at": null,
                    "created_at": "2018-01-06 05:31:35",
                    "updated_at": "2018-01-06 05:31:35",
                    "sub_categories": []
                }
            ]
        }
     */
    public function index_old()
    {
        $user = Auth::user();

        $business_id = $user->business_id;

        $query = Category::where('business_id', $business_id)
                         ->onlyParent()
                        ->with('sub_categories');

        if (! empty(request()->input('type'))) {
            $query->where('category_type', request()->input('type'));
        }

        $categories = $query->get();

        return CommonResource::collection($categories);
    }

    public function index()
    {
        $user = Auth::user();

        $business_id = $user->business_id;

        $query = Category::where('business_id', $business_id)
            ->where('category_type','product')
            ->select('id','name','description'
                , DB::raw("CONCAT('" . asset('uploads/media') . "/', image) AS image_url")
            )->with(['products' => function ($query) {
                $query->join('business','business.id','products.business_id')
                    ->leftjoin('categories','categories.id','products.category_id')
                    ->leftjoin('variation_location_details as vl','vl.product_id','products.id')
                    ->leftjoin('variations as var','var.product_id','products.id')
                    ->select('category_id', 'products.id as product_id','products.name','products.image','products.product_description'
                        , DB::raw("CONCAT('" . asset('uploads/media') . "/', products.image) AS image_url")
                        ,'categories.id as cat_id','categories.name as cat_name','categories.description as cat_desc'
                        ,DB::raw("SUM(qty_available) as stock")
                        ,'var.sell_price_inc_tax as price','default_sales_discount'
                    )->groupby('products.id')
                    ->paginate(50);
            }]);


       /* $query = Category::where('business_id', $business_id)
                         ->onlyParent()
                          ->where('category_type','product')
                         ->select('id','name','description'
                         , DB::raw("CONCAT('" . asset('uploads/media') . "/', image) AS image_url")
                         )->with(['products' => function ($query) {
                           $query->with(['variations'=> function ($query_2) {
                               $query_2->with(['media:id,model_id,file_name'])
                               ->select('product_id','variations.id');
                           }])
                               ->join('business','business.id','products.business_id')
                               ->leftjoin('categories','categories.id','products.category_id')
                               ->leftjoin('variation_location_details as vl','vl.product_id','products.id')
                               ->leftjoin('variations as var','var.product_id','products.id')
                               ->select('category_id','var.id', 'products.id as product_id','products.name','products.product_description'
                                   , DB::raw("CONCAT('" . asset('uploads/media') . "/', products.image) AS image_url")
                                   ,'categories.id as cat_id','categories.name as cat_name','categories.description as cat_desc'
                                   ,DB::raw("SUM(qty_available) as stock")
                                   ,'var.sell_price_inc_tax as price','default_sales_discount'
                                )->groupby('products.id')
                               ->paginate(50);
                         }]);*/


     $categories = $query->get();

        return CommonResource::collection($categories);
    }

    /**
     * Get the specified taxonomy
     *
     * @urlParam taxonomy required comma separated ids of product categories Example: 1

     * @response {
            "data": [
                {
                    "id": 1,
                    "name": "Men's",
                    "business_id": 1,
                    "short_code": null,
                    "parent_id": 0,
                    "created_by": 1,
                    "category_type": "product",
                    "description": null,
                    "slug": null,
                    "woocommerce_cat_id": null,
                    "deleted_at": null,
                    "created_at": "2018-01-03 21:06:34",
                    "updated_at": "2018-01-03 21:06:34",
                    "sub_categories": [
                        {
                            "id": 4,
                            "name": "Jeans",
                            "business_id": 1,
                            "short_code": null,
                            "parent_id": 1,
                            "created_by": 1,
                            "category_type": "product",
                            "description": null,
                            "slug": null,
                            "woocommerce_cat_id": null,
                            "deleted_at": null,
                            "created_at": "2018-01-03 21:07:34",
                            "updated_at": "2018-01-03 21:07:34"
                        },
                        {
                            "id": 5,
                            "name": "Shirts",
                            "business_id": 1,
                            "short_code": null,
                            "parent_id": 1,
                            "created_by": 1,
                            "category_type": "product",
                            "description": null,
                            "slug": null,
                            "woocommerce_cat_id": null,
                            "deleted_at": null,
                            "created_at": "2018-01-03 21:08:18",
                            "updated_at": "2018-01-03 21:08:18"
                        }
                    ]
                }
            ]
        }
     */
    public function show($category_ids)
    {
        $user = Auth::user();

        $business_id = $user->business_id;
        $category_ids = explode(',', $category_ids);

        $query = Category::where('business_id', $business_id)
            ->whereIn('categories.id', $category_ids)
            ->where('category_type','product')
            ->select('id','name','description'
                , DB::raw("CONCAT('" . asset('uploads/media') . "/', image) AS image_url")
            )->with(['products' => function ($query) {
                $query->join('business','business.id','products.business_id')
                    ->leftjoin('categories','categories.id','products.category_id')
                    ->leftjoin('variation_location_details as vl','vl.product_id','products.id')
                    ->leftjoin('variations as var','var.product_id','products.id')
                    ->select('category_id', 'products.id as product_id','products.name','products.image','products.product_description'
                        , DB::raw("CONCAT('" . asset('uploads/media') . "/', products.image) AS image_url")
                        ,'categories.id as cat_id','categories.name as cat_name','categories.description as cat_desc'
                        ,DB::raw("SUM(qty_available) as stock")
                        ,'var.sell_price_inc_tax as price','default_sales_discount'
                    )->groupby('products.id')
                    ->paginate(50);
            }]);
        $categories = $query->get();

        return CommonResource::collection($categories);
    }






    public function media()
    {
        $user = Auth::user();
        $business_id = $user->business_id;

        $data=SlideMedia::where('business_id',$business_id)
            ->select('id','title','order'
                , DB::raw("CONCAT('" . asset('uploads/media') . "/', image_url) AS image_url"))
              ->orderby('order')->get();
        return CommonResource::collection($data);
    }

}
