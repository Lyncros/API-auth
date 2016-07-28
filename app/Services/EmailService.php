<?php
/**
 * Created by PhpStorm.
 * User: lucas
 * Date: 14/7/16
 * Time: 11:00 AM
 */

namespace App\Services;

use Illuminate\Support\Facades\Mail;

class EmailService
{
    public function send(array $data, $template, $subject, $request)
    {
        Mail::send($template, $data,
            function ($message) use ($request, $subject) {
                $message->from(env('CONTACT_FROM'))
                        ->to($request->email, $request->nombre . ' ' . $request->apellido)
                        ->subject($subject);
            });
    }
}