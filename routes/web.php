<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ResidentController;
use App\Http\Controllers\HouseholdController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\ServiceLogController;
use App\Http\Controllers\CitizenRequestController;
use App\Http\Controllers\IssueMappingController;
use App\Http\Controllers\QrVerificationController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ResidentPortalController;

// Auth Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Resident Portal (public-facing)
Route::middleware('maintenance')->group(function () {
Route::get('/', [ResidentPortalController::class, 'home'])->name('portal.home');
Route::get('/portal', [ResidentPortalController::class, 'home'])->name('portal.index');
Route::get('/portal/register', [ResidentPortalController::class, 'register'])->name('portal.register');
Route::post('/portal/register', [ResidentPortalController::class, 'storeRegister']);
Route::get('/portal/login', [ResidentPortalController::class, 'login'])->name('portal.login');
Route::post('/portal/login', [ResidentPortalController::class, 'doLogin']);
Route::post('/portal/logout', [ResidentPortalController::class, 'logout'])->name('portal.logout');

Route::middleware(['auth.resident','maintenance'])->prefix('portal')->name('portal.')->group(function () {
    Route::get('/dashboard', [ResidentPortalController::class, 'dashboard'])->name('dashboard');
    Route::get('/request', [ResidentPortalController::class, 'requestForm'])->name('request');
    Route::post('/request', [ResidentPortalController::class, 'submitRequest'])->name('request.submit');
    Route::get('/report', [ResidentPortalController::class, 'reportForm'])->name('report');
    Route::post('/report', [ResidentPortalController::class, 'submitReport'])->name('report.submit');
    Route::get('/track', [ResidentPortalController::class, 'track'])->name('track');
    Route::get('/track/{tracking}', [ResidentPortalController::class, 'trackDetail'])->name('track.detail');
    Route::get('/announcements', [ResidentPortalController::class, 'announcements'])->name('announcements');
    Route::get('/profile', [ResidentPortalController::class, 'profile'])->name('profile');
    Route::put('/profile', [ResidentPortalController::class, 'updateProfile'])->name('profile.update');
});
}); // end maintenance group


// Admin Routes
Route::middleware('auth.admin')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Residents
    Route::resource('residents', ResidentController::class);
    Route::get('/residents/{resident}/qr', [ResidentController::class, 'generateQr'])->name('residents.qr');

    // Households
    Route::resource('households', HouseholdController::class);
    Route::post('/households/{household}/assign', [HouseholdController::class, 'assignResident'])->name('households.assign');
    Route::delete('/households/{household}/remove/{resident}', [HouseholdController::class, 'removeResident'])->name('households.remove');

    // Documents
    Route::get('/documents/templates', [App\Http\Controllers\DocumentTemplateController::class, 'index'])->name('documents.templates.index');
    Route::get('/documents/templates/{type}/edit', [App\Http\Controllers\DocumentTemplateController::class, 'edit'])->name('documents.templates.edit');
    Route::put('/documents/templates/{type}', [App\Http\Controllers\DocumentTemplateController::class, 'update'])->name('documents.templates.update');
    Route::post('/documents/templates/{type}/reset', [App\Http\Controllers\DocumentTemplateController::class, 'reset'])->name('documents.templates.reset');
    Route::resource('documents', DocumentController::class);
    Route::get('/documents/{document}/print', [DocumentController::class, 'print'])->name('documents.print');
    Route::post('/documents/{document}/reissue', [DocumentController::class, 'reissue'])->name('documents.reissue');

    // Service Logs
    Route::resource('service-logs', ServiceLogController::class);

    // Citizen Requests
    Route::resource('citizen-requests', CitizenRequestController::class);
    Route::post('/citizen-requests/{citizenRequest}/assign', [CitizenRequestController::class, 'assign'])->name('citizen-requests.assign');
    Route::post('/citizen-requests/{citizenRequest}/status', [CitizenRequestController::class, 'updateStatus'])->name('citizen-requests.status');
    Route::post('/citizen-requests/{citizenRequest}/convert', [CitizenRequestController::class, 'convertToServiceLog'])->name('citizen-requests.convert');

    // Issue Mapping
    Route::get('/mapping', [IssueMappingController::class, 'index'])->name('mapping.index');
    Route::get('/mapping/data', [IssueMappingController::class, 'data'])->name('mapping.data');

    // QR Verification
    Route::get('/qr-verify', [QrVerificationController::class, 'index'])->name('qr.index');
    Route::post('/qr-verify', [QrVerificationController::class, 'verify'])->name('qr.verify');

    // Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/residents', [ReportController::class, 'residents'])->name('reports.residents');
    Route::get('/reports/households', [ReportController::class, 'households'])->name('reports.households');
    Route::get('/reports/documents', [ReportController::class, 'documents'])->name('reports.documents');
    Route::get('/reports/services', [ReportController::class, 'services'])->name('reports.services');
    Route::get('/reports/export/{type}/{format}', [ReportController::class, 'export'])->name('reports.export');

    // Announcements
    Route::resource('announcements', AnnouncementController::class);
    Route::post('/announcements/{announcement}/publish', [AnnouncementController::class, 'publish'])->name('announcements.publish');
    Route::post('/announcements/{announcement}/archive', [AnnouncementController::class, 'archive'])->name('announcements.archive');

    // Users
    Route::resource('users', UserController::class);
    Route::post('/users/{user}/deactivate', [UserController::class, 'deactivate'])->name('users.deactivate');

    // Settings
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');
    Route::post('/settings/logo', [SettingController::class, 'uploadLogo'])->name('settings.logo');

    // Profile
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'changePassword'])->name('profile.password');
    Route::post('/profile/avatar', [ProfileController::class, 'uploadAvatar'])->name('profile.avatar');
});
