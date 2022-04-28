<?php

namespace App\Http\Controllers\site\student;

use App\Http\Controllers\Controller;
use App\Models\Billing;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;


class payment extends Controller
{
    public function payment_request(Request $request){
        // validate registeration request
        $validator = Validator::make($request->all(), [
            'amount'     => 'required|integer',
        ]);

        if($validator->fails()){
            return $this::faild($validator->errors()->first(), 403);
        }

        //get student
        if (! $student = auth('student')->user()) {
            return $this::faild(trans('auth.student not found'), 404, 'E04');
        }

        $response = Http::withHeaders([
            'authorization' => env('paytabs_key'),
            'Content-Type'  => 'application/json'
        ])->post('https://secure-egypt.paytabs.com/payment/request', [
            "profile_id"=>         env('paytabs_profile_id'),
            "tran_type"=>          "sale",
            "tran_class"=>         "ecom",
            "cart_description"=>   "AS SDAs",
            "cart_id"=>            time() . rand(1,10000),
            "cart_currency"=>      "EGP",
            "cart_amount"=>        $request->get('amount'),
            "hide_shipping"=>      true,
            "callback"=>           url('api/balanceCharging/' . $student->id),
        ]);

        return $this->success(
            trans('auth.success'),
            200,
            'payment_form_url',
            $response['redirect_url']
        );
    }

    public function balance_charging(Request $request, $student_id){
        $student = Student::find($student_id);

        $response = Http::withHeaders([
            'authorization' => env('paytabs_key'),
            'Content-Type'  => 'application/json'
        ])->post('https://secure-egypt.paytabs.com/payment/query', [
            "profile_id"=>         env('paytabs_profile_id'),
            "tran_ref"=>           $request->get('tran_ref'),
        ]);

        $student->balance += $response["cart_amount"];
        $student->save();

        Billing::create([
            'type' => 0,
            'amount' => $response["cart_amount"],
            'massage'   => 'massage',
            'billingable_id' => $student_id,
            'billingable_type' => 'App\Models\Student',
        ]);

        return true;
    }
}
