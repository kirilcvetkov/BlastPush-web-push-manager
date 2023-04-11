<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\WebsiteController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DialogController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\VariableController;
use App\Http\Controllers\WebhookController;
use App\Http\Controllers\SubscriberController;
use App\Http\Controllers\ImportSubscribersController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\PushController;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\ApiTokenController;
use App\Http\Controllers\ScriptController;
use App\Http\Controllers\ReportsController;
use Rap2hpoutre\LaravelLogViewer\LogViewerController;

use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\PlanController;
use App\Http\Controllers\Admin\UserController;

// Auth::routes(['verify' => true, 'register' => false]);

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test/{website?}', function($websiteUuid = null) {
    if ($websiteUuid && Auth::user()) {
        $website = Auth::user()->websites()->where('uuid', $websiteUuid)->first();
    } else {
        $user = \App\Models\User::first();
        $domain = 'blastpush.com/test';
        $dialog = App\Dialog::where("is_global", true)->first();
        if (! $dialog instanceof App\Dialog) {
            $dialog = factory(App\Dialog::class)->create([
                "is_global" => true,
                "user_id" => $userId,
            ]);
        }
        $website = \App\Website::firstOrCreate([
                "domain" => $domain,
                "user_id" => $user->id,
            ], [
                "name" => "BlastPush Test",
                "domain" => $domain,
                "user_id" => $user->id,
                "dialog_id" => $dialog->id,
            ]
        );
    }
    return view('push', ['website' => $website]);
})->name('test');

// Guarded routes
// Route::middleware(['auth:sanctum', 'verified'])->group(function() {
//     Route::get('/dashboard', function() {
//         return Inertia\Inertia::render('Dashboard');
//     })->name('dashboard');

//     Route::resource('websites', WebsiteController::class);

//     Route::get('menu', function() {
//         return config('menu');
//     });
// });

Route::get('/', function() {
    return view('welcome');
})->name('home');

Route::middleware(['auth:sanctum', 'verified'])->group(function() {
    Route::get('menu', function() {
        return config('menu');
    });
    Route::get('countries', function() {
        return config('countries');
    });

    Route::resource('roles', RoleController::class);
    Route::resource('plans', PlanController::class);
    Route::resource('messages', MessageController::class);

    // Website
    Route::resource('websites', WebsiteController::class);
    Route::resource('dialogs', DialogController::class);
    Route::get('variables', [VariableController::class, 'index'])->name('variables.index');
    Route::post('variables', [VariableController::class, 'store'])->name('variables.store');
    Route::delete('variables/{scope}/{name}/{target?}', [VariableController::class, 'destroy'])->name('variables.delete');
    Route::get('dialogs/preview', [DialogController::class, 'preview'])->name('dialogs.preview');
    Route::get('dialogs/preview/{id?}', [DialogController::class, 'previewId'])->name('dialogs.preview.id');
    Route::get('webhooks', [WebhookController::class, 'preview'])->name('webhooks.index');
    Route::get('websites/webhooks', [WebhookController::class, 'preview'])->name('webhooks.index');
    Route::post('webhooks-test', [WebhookController::class, 'test'])->name('webhooks.test');

    // Subscribers
    Route::get('subscribers', [SubscriberController::class, 'index'])->name("subscribers.index");
    Route::get('subscribers/{subscriber}', [SubscriberController::class, 'show'])->name("subscribers.view");
    Route::get('import', [ImportSubscribersController::class, 'show'])->name("import.show");
    Route::post('import/preview', [ImportSubscribersController::class, 'preview'])->name("import.preview");
    Route::post('import', [ImportSubscribersController::class, 'store'])->name("import.store");

    // Events
    Route::get('events', [EventController::class, 'index'])->name("events.index");
    Route::get('events/{event}', [EventController::class, 'show'])->name("events.view");
    Route::get('events/{event}/webhook', [WebhookController::class, 'show'])->name("events.webhook");

    // Push
    Route::get('push', [PushController::class, 'index'])->name("push.index");
    Route::get('push/{push}', [PushController::class, 'show'])->name("push.view");

    // Campaigns
    Route::resource('campaigns', CampaignController::class);

    // Account
    Route::get('account', [AccountController::class, 'show'])->name('account.show');
    Route::post('account/plan', [AccountController::class, 'planStore'])->name('account.plan');
    Route::get('account/profile', [AccountController::class, 'profile'])->name('profile.edit');
    Route::patch('account/profile/update', [AccountController::class, 'profileUpdate'])->name('profile.update');
    // Route::get('token', 'ApiTokenController@index')->name('token');
    Route::get('token.replace', [ApiTokenController::class, 'update'])->name('token.replace');

    // Scripts
    Route::get('scripts/{website}', [ScriptController::class, 'index'])->name('scripts');
    Route::get('scripts/{website}/download', [ScriptController::class, 'download'])->name('scripts.download');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('dashboard/subscribers/{subscribed}/{timeframe?}', [DashboardController::class, 'subscribers']);
    Route::get('dashboard/subscribersChart/{subscribed}/{timeframe?}', [DashboardController::class, 'subscribersChart']);
    Route::get('dashboard/push/{timeframe?}', [DashboardController::class, 'push']);
    Route::get('dashboard/pushChart/{timeframe?}', [DashboardController::class, 'pushChart']);
    Route::get('dashboard/clicks/{timeframe?}', [DashboardController::class, 'clicks']);
    Route::get('dashboard/clicksChart/{timeframe?}', [DashboardController::class, 'clicksChart']);
    Route::get('dashboard/events', [DashboardController::class, 'events']);
    Route::get('dashboard/subscriptions', [DashboardController::class, 'subscriptions']);
    Route::get('dashboard/browsers/{field?}', [DashboardController::class, 'browsers']);

    // Reports
    Route::get('reports/campaigns', [ReportsController::class, 'campaigns'])->name("reports.campaigns");
    Route::get('reports/websites', [ReportsController::class, 'websites'])->name("reports.websites");
});

Route::middleware(['auth', 'permission:superuser'])->group(function() {
    Route::resource('users', UserController::class);
    Route::patch('password/{user}', [UserController::class, 'password'])->name('user.password');
    Route::get('logs-viewer', [LogViewerController::class, 'index'])->name('logs-viewer');
});

Route::middleware(['cors'])->get('/hub', function() {
    return view('scripts.hub');
});

Route::fallback(function() {
    abort(404);
});
