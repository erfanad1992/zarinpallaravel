<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class HomeController extends Controller
{

    public  $merchantid="cfa83c81-89b0-4993-9445-2c3fcd323455";
    //



    public  function index(){

        return 'hi';
    }


    public function getAmountFromView(Request $request){
        $name = $request->input('fullname');
        $amountvalue= $request->amount;


        return $amountvalue;
    }



    public  function payment(Request $request){

        $request->session()->put('amount', $request->amount);
        $name = $request->input('fullname');
         $amountValue = $this->getAmountFromView($request);
        $mobile = $request->mobile;
        $email = $request->email;

        $description = $request->description;
        if($mobile =="" || $email ==""){
            $data = array("merchant_id" =>$this->merchantid,
                "amount" => $amountValue,
                "callback_url" => "http://127.0.0.1:8000/verifypayment",
                "description" => $description,

            );

        }else {

            $data = array("merchant_id" => $this->merchantid,
                "amount" => $amountValue,
                "callback_url" => "http://127.0.0.1:8000/verifypayment",
                "description" => $description,
                "metadata" => [ "email" => $email,"mobile"=>$mobile],
            );
        }


        $jsonData = json_encode($data);
        $ch = curl_init('https://api.zarinpal.com/pg/v4/payment/request.json');
        curl_setopt($ch, CURLOPT_USERAGENT, 'ZarinPal Rest Api v1');
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($jsonData)
        ));

        $result = curl_exec($ch);
        $err = curl_error($ch);
        $result = json_decode($result, true, JSON_PRETTY_PRINT);
        curl_close($ch);



        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            if (empty($result['errors'])) {
                if ($result['data']['code'] == 100) {

                    return redirect()->away('https://www.zarinpal.com/pg/StartPay/' . $result['data']["authority"]);

                }
            } else {
                echo'Error Code: ' . $result['errors']['code'];
                echo'message: ' .  $result['errors']['message'];

            }
        }
        return $result;
    }

    public  function verifypayment(Request $request){
        $name = $request->input('fullname');
        $Authority = $_GET['Authority'];
        $amountValue= $request->session()->get('amount');

        $data = array("merchant_id" => $this->merchantid, "authority" => $Authority, "amount" => $amountValue);
        $jsonData = json_encode($data);
        $ch = curl_init('https://api.zarinpal.com/pg/v4/payment/verify.json');
        curl_setopt($ch, CURLOPT_USERAGENT, 'ZarinPal Rest Api v4');
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($jsonData)
        ));

        $result = curl_exec($ch);
        $err = curl_error($ch);
        $result = json_decode($result, true);
        curl_close($ch);


        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            if (empty($result['errors'])) {
                if ($result['data']['code'] == 100) {

                    $data = $result['data']['ref_id'];
                    return view('verifypayment', compact('data'));
                } else {
                    $errorcode=$result['errors']['code'];
                    echo "<div style='font-size: xx-large; color: darkred; background-color: rgba(255,113,79,0.67);text-align: center;'> تراکنش ناموفق با کد :$errorcode</div>";


                }
            }else {
                $errorcode=$result['errors']['code'];
                echo "<div style='font-size: xx-large; color: darkred; background-color: rgba(255,113,79,0.67);text-align: center;'> تراکنش ناموفق با کد :$errorcode</div>";

            }

        }
    }



}

