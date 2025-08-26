<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use App\Livewire\Accounts\Index as Aindex;
use App\Livewire\Transactions\Index as Tindex;
use App\Livewire\Categories\Index as Cindex;
use App\Livewire\Budgets\Index as Bindex;
use App\Livewire\Goals\Index as Gindex;

Route::get('/', function () {
    return view('welcome');
})->name('home');



Route::middleware(['auth'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');

    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');

    Route::get('/accounts', Aindex::class)->name('accounts.index');
    Route::get('/transactions', Tindex::class)->name('transactions.index'); 
    Route::get('/categories', Cindex::class)->name('categories.index'); 
    Route::get('/budgets', Bindex::class)->name('budgets.index'); 
    Route::get('/goals', Gindex::class)->name('goals.index');



});

require __DIR__.'/auth.php';
