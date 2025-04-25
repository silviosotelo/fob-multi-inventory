<?php

namespace FriendsOfBotble\MultiInventory\Providers;

use Botble\Dashboard\Facades\DashboardFacade;
use FriendsOfBotble\MultiInventory\Widgets\InventorySummaryWidget;
use Illuminate\Support\ServiceProvider;

class WidgetServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        DashboardFacade::registerWidget(InventorySummaryWidget::class);
    }
}