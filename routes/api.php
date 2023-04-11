<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SubscriberController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\ApplePassController;
use App\Http\Controllers\ApplePassKitController;
use App\Http\Controllers\ScriptController;
use App\Http\Controllers\WebsiteController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\PushController;
use App\Http\Controllers\PushManualController;
use App\Http\Controllers\PushMessageController;
use App\Http\Controllers\WebhookController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

// Routes that allow CORS - no auth - JS calls from client browser
Route::middleware(['cors', 'api.log'])->group(function() {
    // Subscriber
    Route::post('website/{website:uuid}/subscriber', [SubscriberController::class, 'store'])->name("api.subscriber");
    // Route::post('website/{website:uuid}/subscriberID/{subscriber}', [SubscriberController::class, 'showBySubscription']);
    Route::put('website/{website:uuid}/subscriber', [SubscriberController::class, 'store']);
    Route::delete('website/{website:uuid}/subscriber', [SubscriberController::class, 'destroy']);
    // Events
    Route::post('website/{website:uuid}/event', [EventController::class, 'store'])->name("api.event");

    // TODO
    Route::get('website/{website:uuid}/payload', function(Request $request) {
        return $request;
    })->name("api.payload");
    Route::get('website/{website:uuid}/icon', function(Request $request) {
        return response(file_get_contents(asset('img/airplain.png')))
            ->header('Content-Type', 'image/png')
            ->header('Cache-Control', 'no-cache');
    })->name("api.icon");
    Route::get('some_webhook_url', function(Request $request) {
        if (rand(0, 1)) {
            return response()->json('Some error', 400);
        }
        return response()->json('Success', 200);
    });

    // Apple Passes
    Route::get('apple-pass', [ApplePassController::class, 'index']); // Get pass
    Route::get('apple-pass/jwt', [ApplePassController::class, 'jwt']); // Send Push by Token
    Route::get('apple-pass/cert', [ApplePassController::class, 'cert']); // Send Push by Certificate

    ### Apple PassKit ###
    // https://developer.apple.com/library/archive/documentation/PassKit/Reference/PassKit_WebService/WebService.html
    // Registering a Device to Receive Push Notifications for a Pass
    // POST request to webServiceURL/v1/devices/deviceLibraryIdentifier/registrations/passTypeIdentifier/serialNumber
    Route::post(
        'apple-pass/v1/devices/{deviceLibraryIdentifier}/registrations/{passTypeIdentifier}/{serialNumber}',
        [ApplePassKitController::class, 'store']
    );
    // Getting the Serial Numbers for Passes Associated with a Device
    // GET request to webServiceURL/v1/devices/deviceLibraryIdentifier/registrations/passTypeIdentifier?passesUpdatedSince=tag
    Route::get(
        'apple-pass/v1/devices/{deviceLibraryIdentifier}/registrations/{passTypeIdentifier}',
        [ApplePassKitController::class, 'index']
    );
    // Getting the Latest Version of a Pass
    // GET request to webServiceURL/v1/passes/passTypeIdentifier/serialNumber
    Route::get(
        'apple-pass/v1/passes/{passTypeIdentifier}/{serialNumber}',
        [ApplePassKitController::class, 'show']
    );
    // Unregistering a Device
    // DELETE request to webServiceURL/v1/devices/deviceLibraryIdentifier/registrations/passTypeIdentifier/serialNumber
    Route::delete(
        'apple-pass/v1/devices/{deviceLibraryIdentifier}/registrations/{passTypeIdentifier}/{serialNumber}',
        [ApplePassKitController::class, 'destroy']
    );
    // Logging Errors
    // POST request to webServiceURL/v1/log
    Route::post('apple-pass/v1/log', [ApplePassKitController::class, 'log']);
});

Route::middleware(['cors', 'etag'])->group(function() {
    // Scripts
    Route::get('website/{website:uuid}/javascript', [ScriptController::class, 'javascript'])->name("api.javascript");
    Route::get('website/{website:uuid}/serviceworker', [ScriptController::class, 'serviceworker'])->name("api.serviceworker");
});

// Routes that require authentication
Route::middleware(['auth:sanctum', 'api.log'])->group(function() {
    // User
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    // Website
    Route::resource('website', WebsiteController::class);
    // Subscriber
    Route::get('website/{website:uuid}/subscriber', [SubscriberController::class, 'index']);
    // Event
    Route::get('website/{website:uuid}/event', [EventController::class, 'index']);
    Route::get('events/{event}', [EventController::class, 'show']);
    // Message
    Route::resource('message', MessageController::class);
    // Push
    Route::get('push', [PushController::class, 'index']);
    Route::get('website/{website:uuid}/push', [PushController::class, 'indexByWebsite']);
    Route::get('message/{message}/push', [PushController::class, 'indexByMessage']);
    Route::get('subscriber/{subscriber}/push', [PushController::class, 'indexBySubscriber']);
    Route::post('subscriber/{subscriber}/push', [PushManualController::class, 'send']);
    Route::get('subscriber/{subscriber}/message/{message}/push', [PushController::class, 'indexBySubscriberAndMessage']);
    Route::middleware('api.log')->post('subscriber/{subscriber}/push', [PushManualController::class, 'send']);
    Route::middleware('api.log')->post('subscriber/{subscriber}/message/{message}/push', [PushMessageController::class, 'send']);
    // Webhook
    Route::get('website/{website:uuid}/webhook', [WebhookController::class, 'index']);
});

// Route::fallback(function() {
//     return response()->json(['message' => 'Not found.'], 404);
// });

