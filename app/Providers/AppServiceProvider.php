<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Livewire::component('admin.senara-aduan', \App\Livewire\Admin\SenaraAduan::class);
        Livewire::component('admin.aliran-kerja', \App\Livewire\Admin\AliranKerja::class);
        Livewire::component('admin.laporan-bulanan', \App\Livewire\Admin\LaporanBulanan::class);
        Livewire::component('admin.live-monitor', \App\Livewire\Admin\LiveMonitor::class);
    }
}
