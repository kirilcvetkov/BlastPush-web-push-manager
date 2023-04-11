<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Verified;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        Verified::class => [
            \App\Listeners\LogVerifiedUser::class,
        ],
        \App\Events\WebhookCreated::class => [
            \App\Listeners\ProcessWebhook::class,
        ],
        \App\Events\EventCreated::class => [
            \App\Listeners\MatchTriggers::class,
        ],
        \App\Events\SubscriberCreated::class => [
            \App\Listeners\ProcessCampaigns::class,
        ],
        \Illuminate\Mail\Events\MessageSent::class => [
            \App\Listeners\LogSentMessage::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
