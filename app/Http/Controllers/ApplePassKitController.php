<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use PKPass\PKPass;

class ApplePassKitController extends Controller
{
    public function logRequest(Request $request, array $params = [])
    {
        Log::debug(
            'params: ' . var_export($params, true) . "\n" .
            'input: ' . var_export($request->all(), true) . "\n" .
            'body: ' . var_export($request->getContent(), true) . "\n" .
            'header: ' . var_export($request->header(), true) . "\n" .
            'query: ' . var_export($request->query->all(), true) . "\n" .
            'request: ' . var_export($request->request->all(), true) . "\n" .
            'attributes: ' . var_export($request->attributes->all(), true) . "\n" .
            'cookies: ' . var_export($request->cookies->all(), true) . "\n" .
            'server: ' . var_export($request->server->all(), true)
        );
    }

    /**
     * Getting the Serial Numbers for Passes Associated with a Device
     * GET request to webServiceURL/v1/devices/deviceLibraryIdentifier/registrations/passTypeIdentifier?passesUpdatedSince=tag

        Response
        If there are matching passes, returns HTTP status 200 with a JSON dictionary with the following keys and values:
        lastUpdated (string)        The current modification tag.
        serialNumbers (array of strings)        The serial numbers of the matching passes.
        If there are no matching passes, returns HTTP status 204.
        If no update tag is provided, return all the passes that the device is registered for
     */
    public function index(Request $request, $deviceLibraryIdentifier, $passTypeIdentifier)
    {
        $this->logRequest($request, compact(['deviceLibraryIdentifier', 'passTypeIdentifier']));

        return response()->json([
            "serialNumbers" => ["123456"],
            "lastUpdated" => (string)strtotime("-10 min"),
        ], 200);
    }

    /**
     * Registering a Device to Receive Push Notifications for a Pass
     * POST request to webServiceURL/v1/devices/deviceLibraryIdentifier/registrations/passTypeIdentifier/serialNumber

        Response
        If the serial number is already registered for this device, returns HTTP status 200.
        If registration succeeds, returns HTTP status 201.
        If the request is not authorized, returns HTTP status 401.
     */
    public function store($deviceLibraryIdentifier, $passTypeIdentifier, $serialNumber, Request $request)
    {
        $this->logRequest($request, compact(['deviceLibraryIdentifier', 'passTypeIdentifier', 'serialNumber']));

        return response()->json([], 201);
    }

    /**
     * Getting the Latest Version of a Pass
     * GET request to webServiceURL/v1/passes/passTypeIdentifier/serialNumber

        Response
        If request is authorized, returns HTTP status 200 with a payload of the pass data.
        If the request is not authorized, returns HTTP status 401.
        Support standard HTTP caching on this endpoint: Check for the If-Modified-Since header,
        and return HTTP status code 304 if the pass has not changed.
     */
    public function show($passTypeIdentifier, $serialNumber, Request $request)
    {
        $this->logRequest($request, compact(['passTypeIdentifier', 'serialNumber']));

        $certPass = $request->server('CERTIFICATE_PASS') ?: env('CERTIFICATE_PASS');

        $pass = new PKPass(resource_path("apple/BlastPushPass.p12"), $certPass);
        $pass->setWWDRcertPath(resource_path("apple/awdrca.pem"));
        $pass->setData(\Storage::disk('s3')->get('secure/pass.json'));

        // Add files to the pass package
        $pass->addFile(resource_path('apple/images/icon.png'));
        $pass->addFile(resource_path('apple/images/icon@2x.png'));
        $pass->addFile(resource_path('apple/images/logo.png'));
        // $pass->addFile(resource_path('apple/images/strip.png'));
        // $pass->addFile(resource_path('apple/images/strip@2x.png'));
        // $pass->addFile(resource_path('apple/images/background.png'));
        // $pass->addFile(resource_path('apple/images/background@2x.png'));
        $pass->addFile(resource_path('apple/images/thumbnail.png'));
        $pass->addFile(resource_path('apple/images/thumbnail@2x.png'));

        if (! $pass->create()) {
            return response($pass->getError() ?: 'Failed to generate pass', 400);
        }

        // Storage::disk('local')->put('BlastPush.pkpass', $file);

        return $pass->create(true);
    }

    // public function getPassData()
    // {
    //     $data = json_decode(\Storage::disk('s3')->get('secure/pass.json'));
    //     // $data = json_decode(file_get_contents(resource_path("apple/pass.json")), true);

    //     $percent = rand(0, 100);
    //     $data['coupon']['primaryFields'][0]['value'] = "{$percent}% off";
    //     $data['coupon']['primaryFields'][0]['changeMessage'] = "You now have {$percent}% off";

    //     // ,

    //     // $date = date('c', strtotime("+1 Year"));
    //     // $data['coupon']['auxiliaryFields'][0]['value'] = $date;
    //     // $data['coupon']['auxiliaryFields'][0]['changeMessage'] = "Your pass expires on " . date("Y-m-d", strtotime($date));

    //     return json_encode($data);
    // }

    /**
     * Unregistering a Device
     * DELETE request to webServiceURL/v1/devices/deviceLibraryIdentifier/registrations/passTypeIdentifier/serialNumber

        Response
        If disassociation succeeds, returns HTTP status 200.
        If the request is not authorized, returns HTTP status 401.
        Otherwise, returns the appropriate standard HTTP status.
     */
    public function log(Request $request)
    {
        $this->logRequest($request);

        return response()->json([], 200);
    }

    /**
     * Unregistering a Device
     * DELETE request to webServiceURL/v1/devices/deviceLibraryIdentifier/registrations/passTypeIdentifier/serialNumber

        Response
        If disassociation succeeds, returns HTTP status 200.
        If the request is not authorized, returns HTTP status 401.
        Otherwise, returns the appropriate standard HTTP status.
     */
    public function destroy($deviceLibraryIdentifier, $passTypeIdentifier, $serialNumber, Request $request)
    {
        $this->logRequest($request, compact(['deviceLibraryIdentifier', 'passTypeIdentifier', 'serialNumber']));

        return response()->json([], 200);
    }
}
