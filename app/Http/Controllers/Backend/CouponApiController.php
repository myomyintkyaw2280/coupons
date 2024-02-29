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
     * Store a newly created record in storage.
     */
    public function store(Request $request)
    {
        $startTime = microtime(true);
        $method = $request->method();
        $endpoint = $request->path();
        $errors = $validated = [];
        $items = [];
        // Validate the incoming request data
        $validatedData = Validator::make($request->all(),[
            'name' => 'required|string',
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

        // // Perform validation logic
        // $validatedData = Validator::make($request->all(), [
        //     // Define validation rules
        //     // Example:
        //     'name' => 'required|max:128',
        //     'discount_type' => 'required|percentage',
        //     'amount' => 'required|integer',
        //     'code' => 'required|integer',
        //     'start_datetime' => 'date_format:Y-m-d H:i:s',
        //     'end_datetime' => 'date_format:Y-m-d H:i:s',
        //     'coupon_type' => 'in:single-use,multi-use',
        //     // 'used_count' => 'integer',
        // ]);

        // Check if validation fails
        if ($validatedData->fails()) {
            $i = 0;
            foreach ($validatedData->errors() as $key => $value) {
                $validated[$i] = array(
                    'attribute' => $k,
                    'error' => [
                        'key' => $key,
                        'message' => $message
                    ]
                );
                $i++;
            }

            // Loop through each error to retrieve attribute key and message
            /*foreach ($validatedData as $k => $message) {
                // Retrieve the attribute key associated with the error message
                $key = $validatedData[0]; // Assuming you want to retrieve the first error's attribute key
                // $key = $errors->keys()[1]; // Use this if you want to retrieve the second error's attribute key, and so on

                // Output the attribute key and error message
                // echo "Attribute Key: $key, Message: $message";
                $validated[$i] = array(
                    'attribute' => $k,
                    'error' => [
                        'key' => $key,
                        'message' => $message
                    ]
                );
                $i++;
            }*/

            $errors = [
                'message' => 'The request parameters are incorrect, please make sure to follow the documentation about request parameters of the resource.',
                'code' => 400002,
                'validation' => $validated,
            ];
            $success = 0;
            $httpCode = 400;
            // return response()->json([
            //     'success' => 0,
            //     'code' => 400,
            //     'meta' => [
            //         'method' => $request->method(),
            //         'endpoint' => $request->path(),
            //     ],
            //     'data' => $data,
                
            //     'duration' => 0.884
            // ], 400);
        }
        else{
            // // Create a new coupon instance
            // $coupon = new Coupon();
            // $coupon->name = $validatedData['name'];
            // $coupon->description = $validatedData['description'];
            // $coupon->discount_type = $validatedData['discount_type'];
            // $coupon->amount = $validatedData['amount'];
            // $coupon->image_url = $validatedData['image_url'];
            // $coupon->code = $validatedData['code'];
            // $coupon->start_datetime = $validatedData['start_datetime'];
            // $coupon->end_datetime = $validatedData['end_datetime'];
            // $coupon->coupon_type = $validatedData['coupon_type'];
            // $coupon->used_count = $validatedData['used_count'] ?? 0; // Default used_count to 0 if not provided

            // // Save the coupon
            //$saved = $coupon->save();
            if($saved){
                $items = array("id"=> $saved->id);
                $httpCode = 201;
                $success = 1;
            }

        }

            $response = [
                'success' => $success,
                'code' => $httpCode,
                'meta' => [
                    'method' => $method,
                    'endpoint' => $endpoint,
                ],
                'data' => $items,
                'errors' => $errors,
                // 'duration' => $durationFormatted
            ];

        // End time
        $endTime = microtime(true);

        // Calculate duration
        $duration = $endTime - $startTime;
        $durationFormatted = number_format($duration, 3);

        $response['duration'] = $durationFormatted;

        return response()->json($response, $httpCode);
        // return response()->json($response, $httpCode);
    }

    /**
     * Display the specified resource.
     */
    public function getACoupon(Request $request, $adminId, string $id)
    {
        // Start time
        $startTime = microtime(true);
        
        $errors = $meta = [];
        $method = $request->method();
        $endpoint = $request->path();

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
        //
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

        $item = Coupon::find($id);

        if($item){
            $httpCode = 200;
            $success = 1;
            $meta = [
                'method' => $method,
                'endpoint' => $endpoint,            
            ];
            $data = ["deleted" => (int)$id];
            Coupon::destroy($id);
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
