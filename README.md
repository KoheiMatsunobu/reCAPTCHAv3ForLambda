# reCAPTCHA For Lambda Cunstom Runtime(PHP7.4)

## 1.機能概要
- ajaxからAPIGatewayを使用してLambdaをコールし、Google reCAPTCHAv3認証を行う。

## 2.使用言語/環境
- AWS
    - AWS Lambda(Custom Runtime PHP7.4)
    - AWS APIGateway
    - AWS ECR
- Docker

## 3.課題点
S3静的Webホスティングでは、POSTメソッドをサポートしておらず、S3バケット上に保存されている認証機能を実装したファイルに対して、ajaxから自ドメインに対してPOSTリクエストが許可されず403エラーとなってしまう。  

## 4.解決方法
ajaxで外部API(APIGateway)に対してPOSTリクエストし、Lambda関数(Custom Runtime)上でGoogke reCAPTCHAv3認証を行うことで解決できる。  

## 5.インフラ構成
![phplambda-2](https://user-images.githubusercontent.com/58101150/126072462-8b62be00-9f8e-40b0-a1fe-f515f6a8c76a.png)

1. ローカルでDockerを使用して、Lambda Custom Runtime(PHP7.4)ベースのコンテナイメージを作成しビルド。
2. ビルドしたイメージを、事前に作成したECRプライベートリポジトリにpush。
3. LambdaとECRにpushしたリポジトリを連携。
4. jsファイル上のajaxでAPIGatewayをPOSTリクエストする。その際にパラメータにGoogle reCAPTCHAv3認証でサイトキーから生成したトークンを渡す。
5. APIGatewayでPOSTリクエスト時にLambda関数がコールされるように設定。
6. Lambda関数上で、受け取ったトークンとシークレットキーを使用してreCAPTCHAv3認証を行う。認証成功の場合、戻り値をjsファイルに返す。