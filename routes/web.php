<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SettlementController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Groups
    Route::resource('groups', GroupController::class)->except(['edit', 'update']);
    Route::post('/groups/{group}/members', [GroupController::class, 'addMember'])->name('groups.addMember');
    Route::delete('/groups/{group}/members/{user}', [GroupController::class, 'removeMember'])->name('groups.removeMember');

    // Expenses
    Route::get('/groups/{group}/expenses/create', [ExpenseController::class, 'create'])->name('expenses.create');
    Route::post('/groups/{group}/expenses', [ExpenseController::class, 'store'])->name('expenses.store');
    Route::delete('/groups/{group}/expenses/{expense}', [ExpenseController::class, 'destroy'])->name('expenses.destroy');

    // Settlements
    Route::post('/groups/{group}/settlements', [SettlementController::class, 'store'])->name('settlements.store');
    Route::delete('/groups/{group}/settlements/{settlement}', [SettlementController::class, 'destroy'])->name('settlements.destroy');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
