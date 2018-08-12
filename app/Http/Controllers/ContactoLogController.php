<?php

namespace App\Http\Controllers;

use App\ContactoLog;
use App\Mail\ContactUs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactoLogController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $cupon = ContactoLog::orderByDesc("id")->get();
        return response($cupon, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $contacto = new ContactoLog;
        $contacto->nombres = $request->nombres;
        $contacto->email = $request->email;
        $contacto->telefono = $request->telefono;
        $contacto->mensaje = $request->mensaje;
        $contacto->save();

        Mail::send(new ContactUs($contacto));
        return response($contacto, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\ContactoLog  $contactoLog
     * @return \Illuminate\Http\Response
     */
    public function show(ContactoLog $contactoLog)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\ContactoLog  $contactoLog
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ContactoLog $contactoLog)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\ContactoLog  $contactoLog
     * @return \Illuminate\Http\Response
     */
    public function destroy(ContactoLog $contactoLog)
    {
        //
    }
}
