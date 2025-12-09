<?php

namespace App\Http\Services;

use App\Http\Services\Base\CrudService;


class AuthService //extends CrudService
{
    public function sendOtp(string $phone)
    {
        // $code = $this->otpService->generate($phone);
        // if ($mobile == '962778149147') {
        //     return;
        // }
        // $notification = new OtpCodeNotification($mobile, $code, app()->getLocale(), $channel, $suffix);

        // if ($channel === OtpChannel::sms->value && isSyrianNumber($mobile)) {
        //     Notification::route(SmsChannel::class, $mobile)
        //         ->notify($notification);
        // } else {
        //     $notification->channel = OtpChannel::whatsapp->value;
        //     Notification::route(WhatsappChannel::class, $mobile)
        //         ->notify($notification);
        // }
    }
}
