<?php

namespace App\Http\Controllers;

use App\Plan;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Culqi\Culqi;

class PlanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $planes = Plan::all();
        return response($planes, 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $SECRET_KEY = env('CULQUI_PRIVATE_KEY');
        $culqi = new Culqi(array('api_key' => $SECRET_KEY));
        $culqi_plan = $culqi->Plans->create(
            array(
                "amount" => $request->amount,
                "currency_code" => $request->currency_code,
                "interval" => $request->interval,
                "interval_count" => $request->interval_count,
                "name" => $request->name
            )
        );

        $plan = new Plan;
        $plan->culqui_id = $culqi_plan->id;
        $plan->name = $culqi_plan->name;
        $plan->amount = $culqi_plan->amount;
        $plan->currency_code = $culqi_plan->currency_code;
        $plan->interval = $culqi_plan->interval;
        $plan->interval_count = $culqi_plan->interval_count;
        $plan->description = $request->description;
        $plan->default = $request->default;
        $plan->save();

        return response($plan, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $plan = Plan::find($id);
        return response($plan, 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $plan = Plan::find($id);
        $plan->culqui_id = $request->culqui_id;
        $plan->save();
        return response($plan, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $plan = Plan::find($id);
        $plan->delete();
        return response([
            'id'=> $plan->id,
            'deleted'=> true,
            'message' => "Se eliminó el plan con ID ${plan['id']} con exitosamente."
        ], 200);
    }
}
