<?php


if(!function_exists('getErrorMessage')){
	function getErrorMessage($code){
		$message = [
            "400002" =>   "The resource that matches the request ID does not found.",
            "404002" =>   "The resource that matches the request ID does not found.",
            "404003" =>   "The updating resource that corresponds to the ID wasn't found.",
            "404004" =>   "The deleting resource that corresponds to the ID wasn't found.",
            "409001" =>   "The inserting resource was already registered.",
        ];

        return ($message[$code])?$message[$code]:"Invalid Error Code";
	}
}


if(!function_exists('makeRespFormat')){
	function makeRespFormat($success, $httpCode, $meta=[], $data=[], $errors=[]){

		$response = [
            'success' => $success,
            'code' => $httpCode,
            'meta' => $meta,
            'data' => $data,
            'errors' => $errors,
            // 'duration' => $durationFormatted
        ];
		return $response;
	}
}