<?php

namespace HappySoftware\Controller;

class HelperController{

    public static function successResponse($data, $codeResponse = 200)
    {

        $responseData['data'] = $data;
        $responseData['status'] = [];
        $responseData['status']['response'] = 'ok';
        $responseData['status']['code'] = $codeResponse;

        if(isset($responseData['data']['total']))
        {
          //  $responseData['draw'] = 1; // Draw count para prevenir XSS ataques
            $responseData['recordsTotal'] = $responseData['data']['total'];
            $responseData['recordsFiltered'] = $responseData['data']['total'];
        }

        return json_encode($responseData);
    }

    public static function errorResponse($data, $mensaje, $codeResponse)
    {
        $responseData['data'] = $data;
        $responseData['status'] = [];
        $responseData['status']['response'] = 'error';
        $responseData['status']['error'] = $mensaje;
        $responseData['status']['code'] = $codeResponse;
        return json_encode($responseData);
    }

}