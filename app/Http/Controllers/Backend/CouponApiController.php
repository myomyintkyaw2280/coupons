<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use DB;
use App\Models\Admin;
use App\Models\Coupon;
use App\Models\Shop;

class CouponApiController extends Controller
{
    protected $limit = 30;
    protected $resHeader = [
                'Content-Type' => "application/json; charset=utf-8",
            ];
    /**
     * Display a listing of the resource.
     */
    public function getCoupons(Request $request, $adminId)
    {
        // Start time
        $startTime = microtime(true);
        
        $errors = $meta = [];
        $offset = ($request->query('page') === null)? 0:(int)$request->query('page');
        $method = $request->method();
        $endpoint = $request->path();

        $total = Coupon::where(['admin_id' => $adminId])->count();
        $items = DB::table('coupons')
            ->where(['admin_id' => $adminId, 'deleted_at' =>null])
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
    public function getACoupon(Request $request, $adminId, string $id)
    {
        // Start time
        $startTime = microtime(true);
        
        $errors = $meta = [];
        $meta = [
            'method' => $request->method(),
            'endpoint' => $request->path(),
        ];

        $item = Coupon::find($id);

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
     * Store a newly created record in storage.
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
        $data = [];
        $inputs = $request->all();
        // Validate the incoming request data
        $validatedData = Validator::make($inputs,[
            'name' => 'required|string|max:128',
            'description' => 'nullable|string',
            'discount_type' => 'required',
            'amount' => 'required|integer',
            'image_url' => 'nullable|string',
            'code' => 'nullable|integer',
            'start_datetime' => 'nullable|date',
            'end_datetime' => 'nullable|date',
            'coupon_type' => 'required',
            'used_count' => 'nullable|integer',
        ]);

        // Check if validation fails
        if ($validatedData->fails()) {
            $i = 0;

            foreach ($validatedData->errors()->toArray() as $key => $value) {
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
            // Create a new coupon instance
            $coupon = new Coupon();
            $coupon->admin_id = (int)$adminId;
            $coupon->name = $inputs['name'];
            $coupon->description = $inputs['description'];
            $coupon->discount_type = $inputs['discount_type'];
            $coupon->amount = $inputs['amount'];
            $coupon->image_url = $inputs['image_url'];
            $coupon->code = $inputs['code'];
            $coupon->start_datetime = $inputs['start_datetime'];
            $coupon->end_datetime = $inputs['end_datetime'];
            $coupon->coupon_type = $inputs['coupon_type'];
            $coupon->used_count = $inputs['used_count'] ?? 0; // Default used_count to 0 if not provided

            // // Save the coupon
            $saved = $coupon->save();
            if($saved){
                $data = array("id"=> $coupon->id);
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
        $items = [];
        $inputs = $request->all();
        // Validate the incoming request data
        $validatedData = Validator::make($inputs,[
            'name' => 'required|string|max:128',
            'description' => 'nullable|string',
            'discount_type' => 'required',
            'amount' => 'required|integer',
            'image_url' => 'nullable|string',
            'code' => 'nullable|integer',
            'start_datetime' => 'nullable|date',
            'end_datetime' => 'nullable|date',
            'coupon_type' => 'required',
            'used_count' => 'nullable|integer',
        ]);

        // Check if validation fails
        if ($validatedData->fails()) {
            $i = 0;

            foreach ($validatedData->errors()->toArray() as $key => $value) {
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
            $coupon = Coupon::find($id);
            if($coupon){
                $coupon->admin_id = (int)$adminId;
                $coupon->name = $inputs['name'];
                $coupon->description = $inputs['description'];
                $coupon->discount_type = $inputs['discount_type'];
                $coupon->amount = $inputs['amount'];
                $coupon->image_url = $inputs['image_url'];
                $coupon->code = $inputs['code'];
                $coupon->start_datetime = $inputs['start_datetime'];
                $coupon->end_datetime = $inputs['end_datetime'];
                $coupon->coupon_type = $inputs['coupon_type'];
                $coupon->used_count = $inputs['used_count'] ?? 0; // Default used_count to 0 if not provided

                // // Save the coupon
                $update = $coupon->update();
                if($update){
                    $data = array("id"=> $id);
                    $httpCode = 201;
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
        $meta = [
            'method' => $request->method(),
            'endpoint' => $request->path(),
        ];

        $item = Coupon::find($id);

        if($item){
            $httpCode = 200;
            $success = 1;
            $data = ["deleted" => (int)$id];
            Coupon::destroy($id);
        }else{
            $httpCode = 404;
            $success = 0;
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
