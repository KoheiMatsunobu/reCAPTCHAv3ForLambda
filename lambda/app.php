<?php
require './recaptchavars.php';

function handler($event, $context) {

    echo json_encode($context);
    $tokenData = $event['token'];
    
    // reCAPTCHA認証
    $authData = json_decode(
        auth_recapthca($tokenData)
    );

    return response(
        $authData->statusCode,
        $authData->body
    );
}

function response($code, $body) {
    $headers = array(
        "Context-Type"=>"application/json"
    );

    return json_encode(array(
        "statusCode"=>200,
        "headers"=>$headers,
        "code"=>$code,
        "body"=>$body
    ));
}

// reCAPTCHA認証
function auth_recapthca($token) {
    // スコア値のデフォルト設定
    $gscore = V3_SCORE;

    if (isset($token) && !empty($token)) {
        $secret = V3_SECRETKEY;

        // 生成したtokenとシークレットキーを使用して、Google reCAPTCHA認証を行う
        $verifyResponse = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret=' . $secret . '&response=' . $_POST['recaptchaResponse']);

        $responseData = json_decode($verifyResponse);

        if ($responseData->success) {
            if ($responseData->score < $gscore) {
                // 認証スコアが低いのでbotの可能性
                return json_encode(array(
                    "status" => false,
                    "statusCode" =>1,
                    "body" => "Low certification score"
                ));
            }
        } else {
            // 認証失敗した場合
            return json_encode(array(
                "status" => false,
                "statusCode" => 2,
                "body" => "reCAPTCHAv3 Authentication failure"
            ));
        }
    } else {
        // POST値が正常に投げられていなかった場合
        return json_encode(array(
            "status" => false,
            "statusCode" => 3,
            "body"=> "Invalid POST value"
        ));
    }

    // 認証成功
    return json_encode(array(
        "status" => true,
        "statusCode" => 0,
        "body" => "reCAPTCHA Authentication success"
    ));
}