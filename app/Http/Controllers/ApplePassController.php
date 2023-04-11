<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use PKPass\PKPass;

use Apple\ApnPush\Certificate\Certificate;
use Apple\ApnPush\Protocol\Http\Authenticator\CertificateAuthenticator;

use Apple\ApnPush\Jwt\Jwt;
use Apple\ApnPush\Protocol\Http\Authenticator\JwtAuthenticator;

use Apple\ApnPush\Sender\Builder\Http20Builder;
use Apple\ApnPush\Model\Payload;
use Apple\ApnPush\Model\Notification;
use Apple\ApnPush\Model\DeviceToken;
use Apple\ApnPush\Model\Receiver;
use Apple\ApnPush\Exception\SendNotification\SendNotificationException;

class ApplePassController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        dd(\Storage::disk('s3')->put('secure/pass.json', file_get_contents(resource_path("apple/pass.json"))));

        $certPass = $request->server('CERTIFICATE_PASS') ?: env('CERTIFICATE_PASS');

        $pass = new PKPass(resource_path("apple/BlastPushPass.p12"), $certPass);
        $pass->setWWDRcertPath(resource_path("apple/awdrca.pem"));
        $pass->setData(file_get_contents(resource_path("apple/pass.json")));

        // Add files to the pass package
        $pass->addFile(resource_path('apple/images/icon.png'));
        $pass->addFile(resource_path('apple/images/icon@2x.png'));
        $pass->addFile(resource_path('apple/images/logo.png'));

        if (! $pass->create()) {
            return response($pass->getError() ?: 'Failed to generate pass', 400);
        }

        // Storage::disk('local')->put('BlastPush.pkpass', $file);

        return $pass->create(true);
    }

    public function jwt()
    {
        // Create authenticator system
        $jwt = new Jwt('523NXDUA2N', '8UJPU6QVNS', resource_path("apple/AuthKey_8UJPU6QVNS.p8"));
        $authenticator = new JwtAuthenticator($jwt);

        // Create sender with builder
        $builder = new Http20Builder($authenticator);
        $builder->addDefaultVisitors();

        $sender = $builder->build();

        // Create alert, aps, payload, and notification for send to device
        $payload = Payload::createWithBody('Hello ;) Send notification with JWT authentication.');
        $notification = new Notification($payload);

        $receiver = new Receiver(
            new DeviceToken('e3f5cdbdbb00e95facd54e71ca4dd12b6a6ef9b59394c30e4a5478985186c0a7'),
            'pass.com.blastpush'
        );

        try {
            $sender->send($receiver, $notification);
            return response()->json(['Oh... Success send notification'], 200);
        } catch (SendNotificationException $e) {
            return response()->json(['Oops... Fail send message: ' . $e->getMessage()], 400);
        }
    }

    public function cert(Request $request)
    {
        $certPass = $request->server('CERTIFICATE_PASS') ?: env('CERTIFICATE_PASS');

        // Create authenticator system
        $certificate = new Certificate(resource_path("apple/BlastPushPass.pem"), $certPass);
        $authenticator = new CertificateAuthenticator($certificate);

        // Create sender with builder
        $builder = new Http20Builder($authenticator);
        $builder->addDefaultVisitors();

        $sender = $builder->build();

        // Create alert, aps, payload, and notification for send to device
        $payload = Payload::createWithBody('Hello ;) Send notification with certificate authentication.');
        $notification = new Notification($payload);

        $receiver = new Receiver(
            new DeviceToken('e3f5cdbdbb00e95facd54e71ca4dd12b6a6ef9b59394c30e4a5478985186c0a7'),
            'pass.com.blastpush'
        );

        try {
            $sender->send($receiver, $notification);
            return response()->json(['Oh... Success send notification'], 200);
        } catch (SendNotificationException $e) {
            return response()->json(['Oops... Fail send message: ' . $e->getMessage()], 400);
        }
    }
}
