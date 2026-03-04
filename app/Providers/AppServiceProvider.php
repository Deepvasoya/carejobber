<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\ChatConversation;

class AppServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Pass chat unread count to header icon so badge shows instantly on every page
        View::composer('includes.chat-header-icon', function ($view) {
            $chatUnreadCount = 0;
            if (Auth::check()) {
                $chatUnreadCount = (int) ChatConversation::where('user_id', Auth::id())
                    ->sum('unread_count_user');
            } elseif (Auth::guard('company')->check()) {
                $chatUnreadCount = (int) ChatConversation::where('company_id', Auth::guard('company')->id())
                    ->sum('unread_count_company');
            }
            $view->with('chatUnreadCount', $chatUnreadCount);
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

}
