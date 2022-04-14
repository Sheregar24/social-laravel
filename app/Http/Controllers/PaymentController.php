<?php

namespace App\Http\Controllers;

use App\Models\PaymentDetail;
use Razorpay\Api\Api;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    private $razorpayId = "rzp_test_h4dNQh2rnVLHl5";
    private $razorpayKey = "K190ZqxwsKwyHe0aFvIRETEt";

    public function Initiate(Request $request)
    {
        // Generate random receipt id
        $receiptId = Carbon::today();

        // Create an object of razorpay
        $api = new Api($this->razorpayId, $this->razorpayKey);

        // In razorpay you have to convert rupees into paise we multiply by 100
        // Currency will be INR
        // Creating order
        $order = $api->order->create(array(
            'receipt' => $receiptId,
            'amount' => $request->all()['amount'] * 100,
            'currency' => 'INR'
            )
        );

        // Return response on payment page
        $response = [
            'orderId' => $order['id'],
            'razorpayId' => $this->razorpayId,
            'amount' => $request->all()['amount'] * 100,
            'name' => $request->all()['name'],
            'currency' => 'INR',
            'email' => $request->all()['email'],
            'contactNumber' => $request->all()['contactNumber'],
            'address' => $request->all()['address'],
            'description' => 'Testing description',
        ];

        // Let's checkout payment page is it working
        return view('payment-page',compact('response'));
    }

    public function Complete(Request $request)
    {
        // Now verify the signature is correct . We create the private function for verify the signature
        $signatureStatus = $this->SignatureVerify(
            $request->all()['rzp_signature'],
            $request->all()['rzp_paymentid'],
            $request->all()['rzp_orderid']
        );

        // If Signature status is true We will save the payment response in our database
        // In this tutorial we send the response to Success page if payment successfully made
        if($signatureStatus == true)
        {
            // You can create this page
            $paymentdetails = PaymentDetail::create([
                'razorpay_order_id' => $request->all()['rzp_orderid'],
                'razorpay_payment_id' => $request->all()['rzp_paymentid'],
                'razorpay_signature' => $request->all()['rzp_signature'],
                'status' => 1
            ]);
            return view('payment-success-page',['data'=>$request->all()['rzp_paymentid']]);
        }
        else{
            // You can create this page
            return view('payment-failed-page');
        }
    }

    // In this function we return boolean if signature is correct
    private function SignatureVerify($_signature,$_paymentId,$_orderId)
    {
        try
        {
            // Create an object of razorpay class
            $api = new Api($this->razorpayId, $this->razorpayKey);
            $attributes  = array('razorpay_signature'  => $_signature,  'razorpay_payment_id'  => $_paymentId ,  'razorpay_order_id' => $_orderId);
            $order  = $api->utility->verifyPaymentSignature($attributes);
            return true;
        }
        catch(\Exception $e)
        {
            // If Signature is not correct its give a excetption so we use try catch
            return false;
        }
    }

}
