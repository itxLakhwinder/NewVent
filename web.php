<?php

use Illuminate\Support\Facades\Route;
use App\Models\Page;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/vent-center', function () {
    return view('vent-center');
});
Route::get('/privacy-policy', function () {
    $text = Page::where('page', '=', 'privacy-policy')->first();
    return view('privacy-policy')->with([ "text" => $text]);
});
Route::get('/posting-guidelines', function () {
	$text = Page::where('page', '=', 'posting-guidelines')->first();
    return view('posting-guidelines')->with([ "text" => $text]);
});


Route::get('/terms-policies', function () {
	$text = Page::where('page', '=', 'terms')->first();
    return view('terms-policies')->with([ "text" => $text]);
});
Route::get('/download-app', function () {
	$text = Page::where('page', '=', 'terms')->first();
    return view('download-app')->with([ "text" => $text]);
});
Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/users', 'Backend\UserController@index')->name('users.list');

Route::get('/user/{id}', 'Backend\UserController@view');
Route::get('/user/delete/{id}', 'Backend\UserController@delete');
Route::get('/user/disable/{id}', 'Backend\UserController@disable');
Route::get('/user/enable/{id}', 'Backend\UserController@enable');

// Resources
Route::get('/resources', 'Backend\ResourceController@index')->name('resources.list');
Route::get('/resource/delete/{id}', 'Backend\ResourceController@delete');
Route::post('/resources/add', 'Backend\ResourceController@store')->name('resources.add');
Route::post('/resources/update', 'Backend\ResourceController@update')->name('resources.update');

// Partner

Route::get('/partners', 'Backend\PartnerController@index')->name('partners.list');
Route::get('/partner/delete/{id}', 'Backend\PartnerController@delete');
Route::post('/partners/add', 'Backend\PartnerController@store')->name('partners.add');
Route::post('/partners/update', 'Backend\PartnerController@update')->name('partners.update');


// Notifications
Route::get('/notifications', 'Backend\NotificationController@index')->name('notifications.list');
Route::get('/notification/delete/{id}', 'Backend\NotificationController@delete');
Route::post('/notifications/add', 'Backend\NotificationController@store')->name('notifications.add');

// Questions
Route::get('/questions', 'Backend\QuestionController@index')->name('questions.list');
Route::get('/question/delete/{id}', 'Backend\QuestionController@delete');
Route::post('/questions/add', 'Backend\QuestionController@store')->name('questions.add');
Route::post('/questions/update', 'Backend\QuestionController@update')->name('questions.update');
Route::get('/question/disable/{id}', 'Backend\QuestionController@disable');
Route::get('/question/enable/{id}', 'Backend\QuestionController@enable');

// Topics
Route::get('/topics', 'Backend\TopicController@index')->name('topics.list');
Route::get('/topic/{id}', 'Backend\TopicController@view')->name('topics.view');
Route::get('/topic/delete/{id}', 'Backend\TopicController@delete');
Route::post('/topics/add', 'Backend\TopicController@store')->name('topics.add');
Route::post('/topics/update', 'Backend\TopicController@update')->name('topics.update');
Route::get('/topic/disable/{id}', 'Backend\TopicController@disable');
Route::get('/topic/enable/{id}', 'Backend\TopicController@enable');

Route::get('/supports', 'Backend\SupportController@index')->name('supports.list');
Route::get('/support/{id}', 'Backend\SupportController@view')->name('supports.view');
Route::get('/support/delete/{id}', 'Backend\SupportController@delete');
Route::post('/supports/add', 'Backend\SupportController@store')->name('supports.add');
Route::post('/supports/update', 'Backend\SupportController@update')->name('supports.update');
Route::get('/support/disable/{id}', 'Backend\SupportController@disable');
Route::get('/support/enable/{id}', 'Backend\SupportController@enable');



Route::get('/available_topics', 'Backend\TopicController@availableTopics')->name('available_topics.list');
Route::get('/available_topics/delete/{id}', 'Backend\TopicController@availableTopicsDelete');
Route::post('/available_topics/add', 'Backend\TopicController@availableTopicsAdd')->name('available_topics.add');
Route::post('/available_topics/update', 'Backend\TopicController@availableTopicsUpdate')->name('available_topics.update');


Route::get('/categories', 'Backend\CategoryController@index')->name('categories.list');
Route::get('/categories/delete/{id}', 'Backend\CategoryController@delete');
Route::post('/categories/add', 'Backend\CategoryController@store')->name('categories.add');
Route::post('/categories/update', 'Backend\CategoryController@update')->name('categories.update');

Route::get('/mentalhealth', 'Backend\CategoryController@mIndex')->name('mentalhealth.list');
Route::get('/mentalhealth/delete/{id}', 'Backend\CategoryController@mDelete');
Route::post('/mentalhealth/add', 'Backend\CategoryController@mStore')->name('mentalhealth.add');
Route::post('/mentalhealth/update', 'Backend\CategoryController@mUpdate')->name('mentalhealth.update');

// Journals
Route::get('/journals', 'Backend\JournalController@index')->name('journals.list');
Route::get('/journal/{id}', 'Backend\JournalController@view')->name('journals.view');
Route::get('/journal/delete/{id}', 'Backend\JournalController@delete');
Route::get('/journal/disable/{id}', 'Backend\JournalController@disable');
Route::get('/journal/enable/{id}', 'Backend\JournalController@enable');


// Reports
Route::get('/reports', 'Backend\UserController@reports')->name('reports.list');

// FAQs
Route::get('/faqs', 'Backend\FaqController@index')->name('faqs.list');
Route::get('/faq/delete/{id}', 'Backend\FaqController@delete');
Route::post('/faqs/add', 'Backend\FaqController@store')->name('faqs.add');
Route::post('/faqs/update', 'Backend\FaqController@update')->name('faqs.update');
Route::get('/faq/disable/{id}', 'Backend\FaqController@disable');
Route::get('/faq/enable/{id}', 'Backend\FaqController@enable');

// Pages
Route::get('/terms', 'Backend\FaqController@terms')->name('terms');
Route::post('/terms-save', 'Backend\FaqController@termsSave')->name('terms.save');

Route::get('/policy', 'Backend\FaqController@privacyPolicy')->name('policy');
Route::post('/privacy-policy-save', 'Backend\FaqController@privacyPolicySave');

Route::get('/guidelines', 'Backend\FaqController@postingGuidelines')->name('guidelines');
Route::post('/posting-guidelines-save', 'Backend\FaqController@postingGuidelinesSave');