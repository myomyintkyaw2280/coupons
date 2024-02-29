<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $adminId)
    {
        $startTime = microtime(true);
        $meta = [
            'method' => $request->method(),
            'endpoint' => $request->path(),
        ];
        $success = 0;
        $errors = $validated = [];
        $data  = [];
        $inputs = $request->all();

        // Validate the incoming request data
        $validate = Validator::make($inputs,[
            'name' => 'string|nullable',
            'query' => 'string|nullable',
            'latitude' => 'numeric|nullable',
            'longitude' => 'numeric|nullable',
            'zoom' => 'string|nullable',
        ]);

        // Check if validation fails
        if ($validate->fails()) {
            $i = 0;

            foreach ($validate->errors()->toArray() as $key => $value) {
                $validated[$i] = array(
                    'attribute' => $key,
                    'error' => [
                        'key' => $key,
                        'message' => $value[0]
                    ]
                );
                $i++;
            }

            $errors = [
                'message' => getErrorMessage(400002),
                'code' => 400002,
                'validation' => $validated,
            ];
            $success = 0;
            $httpCode = 400;
        }
        else{
            // Create a new Coupon instance
            $shop = new Shop();
            $shop->admin_id = $adminId;
            $shop->name = $inputs['name'];
            $shop->query = $inputs['query'];
            $shop->latitude = $inputs['latitude'];
            $shop->longitude = $inputs['longitude'];
            $shop->zoom = $inputs['zoom'];

            // // Save the coupon
            $saved = $shop->save();
            if($saved){
                $data = array("id"=> $shop->id);
                $httpCode = 201;
                $success = 1;
            }

        }

        $response = makeRespFormat($success, $httpCode, $meta, $data, $errors);
        // End time
        $endTime = microtime(true);

        // Calculate duration
        $duration = $endTime - $startTime;
        $durationFormatted = number_format($duration, 3);

        $response['duration'] = $durationFormatted;

        return response()->json($response, $httpCode);
    }

    /**
     * Update the specified resource in storage.
     */

    public function update(Request $request, $adminId, string $id)
    {
        $startTime = microtime(true);
        $meta = [
            'method' => $request->method(),
            'endpoint' => $request->path(),
        ];
        $errors = $validated = [];
        $inputs = $request->all();

        // Validate the incoming request data
        $validate = Validator::make($inputs,[
            'name' => 'string|nullable',
            'query' => 'string|nullable',
            'latitude' => 'numeric|nullable',
            'longitude' => 'numeric|nullable',
            'zoom' => 'string|nullable',
        ]);

        // Check if validation fails
        if ($validate->fails()) {
            $i = 0;

            foreach ($validate->errors()->toArray() as $key => $value) {
                $validated[$i] = array(
                    'attribute' => $key,
                    'error' => [
                        'key' => $key,
                        'message' => $value[0]
                    ]
                );
                $i++;
            }

            $errors = [
                'message' => getErrorMessage(400002),
                'code' => 400002,
                'validation' => $validated,
            ];
            $success = 0;
            $httpCode = 400;
            $data = [];
        }
        else{
            $shop = Shop::find($id);
            if($shop){
                $shop->admin_id = $adminId;
                $shop->name = $inputs['name'];
                $shop->query = $inputs['query'];
                $shop->latitude = $inputs['latitude'];
                $shop->longitude = $inputs['longitude'];
                $shop->zoom = $inputs['zoom'];

                // // Save the shop
                $update = $shop->update();
                if($update){
                    $data = array("updated"=> $id);
                    $httpCode = 200;
                    $success = 1;
                }
            }else{

                $httpCode = 404;
                $success = 0;
                $data = [];
                $errors = array(
                    "message" => getErrorMessage(404002),
                    "code" => 404002
                );

            }

        }

        $response = makeRespFormat($success, $httpCode, $meta, $data, $errors);
        // End time
        $endTime = microtime(true);

        // Calculate duration
        $duration = $endTime - $startTime;
        $durationFormatted = number_format($duration, 3);

        $response['duration'] = $durationFormatted;

        return response()->json($response, $httpCode);
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
