<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Admin;
use App\Models\Coupon;
use App\Models\Shop;
use DB;

class ShopApiController extends Controller
{
    protected $limit = 30;

    protected $resHeader = [
                'Content-Type' => "application/json; charset=utf-8",
            ];
    /**
     * Display a listing of the resource.
     */
    public function getShops(Request $request, $adminId)
    {
        // Start time
        $startTime = microtime(true);
        
        $errors = $meta = [];
        $offset = ($request->query('page') === null)? 0:(int)$request->query('page');
        $method = $request->method();
        $endpoint = $request->path();

        $total = Shop::where(['admin_id' => $adminId])->count();
        $items = DB::table('shops')
            ->where(['admin_id' => $adminId])
            ->skip($offset * $this->limit)
            ->take($this->limit)
            ->get();

        if($items && $total){
            $httpCode = 200;
            $success = 1;
            $meta = [
                'method' => $method,
                'endpoint' => $endpoint,
                'limit' => $this->limit,
                'offset' => $offset,
                'total' => $total,
            ];

        }else{
            $httpCode = 500;
            $success = 0;
            $meta = [
                'method' => $method,
                'endpoint' => $endpoint,
                'limit' => $this->limit,
                'offset' => $offset,
                'total' => $total,
            ];
        }

        //Response format
        $response = makeRespFormat($success, $httpCode, $meta, $items, $errors);
        
        // End time
        $endTime = microtime(true);

        // Calculate duration
        $duration = $endTime - $startTime;
        $durationFormatted = number_format($duration, 3);

        $response['duration'] = $durationFormatted;

        return response()->json($response, $httpCode)->withHeaders($this->resHeader);
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
    public function getAShop(Request $request, $adminId, string $id)
    {

        // Start time
        $startTime = microtime(true);
        
        $errors = $meta = [];
        $method = $request->method();
        $endpoint = $request->path();

        $item = Shop::find($id);

        if($item){
            $httpCode = 200;
            $success = 1;
            $meta = [
                'method' => $method,
                'endpoint' => $endpoint,            
            ];
            $data = $item;
        }else{
            $httpCode = 404;
            $success = 0;
            $meta = [
                'method' => $method,
                'endpoint' => $endpoint
            ];
            $data = [];
            $errors  = [
                "message" => getErrorMessage(404002),
                "code" => 404002
            ];
        }

        //Response format
        $response = makeRespFormat($success, $httpCode, $meta, $data, $errors);
        // End time
        $endTime = microtime(true);

        // Calculate duration
        $duration = $endTime - $startTime;
        $durationFormatted = number_format($duration, 3);

        return response()->json($response)->withHeaders($this->resHeader);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Start time
        $startTime = microtime(true);
        
        $errors = $meta = [];
        $method = $request->method();
        $endpoint = $request->path();

        $shop = Shop::findOrFail($id);

        // End time
        $endTime = microtime(true);

        // Calculate duration
        $duration = $endTime - $startTime;
        $durationFormatted = number_format($duration, 3);

        return response()->json($response, $httpCode)->withHeaders($this->resHeader);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $adminId, string $id)
    {
        // Start time
        $startTime = microtime(true);
        
        $errors = $meta = [];
        $method = $request->method();
        $endpoint = $request->path();

        $item = Shop::find($id);

        if($item){
            $httpCode = 200;
            $success = 1;
            $meta = [
                'method' => $method,
                'endpoint' => $endpoint,            
            ];
            $data = ["deleted" => (int)$id];
            Shop::destroy($id);
        }else{
            $httpCode = 404;
            $success = 0;
            $meta = [
                'method' => $method,
                'endpoint' => $endpoint
            ];
            $data = [];
            $errors  = [
                "message" => getErrorMessage(404002),
                "code" => 404002
            ];
        }

        //Response format
        $response = makeRespFormat($success, $httpCode, $meta, $data, $errors);
        // End time
        $endTime = microtime(true);

        // Calculate duration
        $duration = $endTime - $startTime;
        $durationFormatted = number_format($duration, 3);

        return response()->json($response)->withHeaders($this->resHeader);
    }
}
