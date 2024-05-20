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
    return view('partner-login');
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

Route::get('/share/{id}', 'HomeController@share')->name('topic.share');
Route::get('/topic-share/{id}', 'HomeController@share')->name('topic.share');

Route::group(['middleware' => ['auth:web' , 'admin']], function () {  

    Route::get('/users', 'Backend\UserController@index')->name('users.list');
    Route::post('/filtered-list', 'Backend\UserController@FilterUsers')->name('users.filtered-list');

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
    Route::post('/partners/approve', 'Backend\PartnerController@approve')->name('partners.approve');
    Route::post('/partners/disapprove', 'Backend\PartnerController@disapprove')->name('partners.disapprove');


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
    Route::post('/filtered-topic-list', 'Backend\TopicController@filteredTopics')->name('topics.filtered-list');
    Route::get('/topic/{id}', 'Backend\TopicController@view')->name('topics.view');
	Route::get('/comment/delete/{id}', 'Backend\TopicController@commentDelete');
    Route::get('/topic/delete/{id}', 'Backend\TopicController@delete');
    Route::post('/topics/add', 'Backend\TopicController@store')->name('topics.add');
    Route::post('/topics/update', 'Backend\TopicController@update')->name('topics.update');
    Route::get('/topic/disable/{id}', 'Backend\TopicController@disable');
    Route::get('/topic/enable/{id}', 'Backend\TopicController@enable');

    //Admin Posts
    Route::get('/admin-posts', 'Backend\TopicController@adminPostsList')->name('admin-posts.list');
    Route::post('admin-posts/add','Backend\TopicController@createAdminPosts')->name('admin-posts.add');
    Route::post('/admin-posts/update', 'Backend\TopicController@updateAdminPosts')->name('admin-posts.update');
    Route::get('/admin-posts/disable/{id}', 'Backend\TopicController@disableAdminPosts');
    Route::get('/admin-posts/enable/{id}', 'Backend\TopicController@enableAdminPosts');
    Route::get('/admin-posts/{id}', 'Backend\TopicController@viewAdminPosts')->name('admin-posts.view');
    Route::get('/admin-posts/delete/{id}', 'Backend\TopicController@deleteAdminPosts');

    //Support
    Route::get('/supports', 'Backend\SupportController@index')->name('supports.list');
    Route::get('/support/{id}', 'Backend\SupportController@view')->name('supports.view');
    Route::get('/support/delete/{id}', 'Backend\SupportController@delete');
    Route::post('/supports/add', 'Backend\SupportController@store')->name('supports.add');
    Route::post('/supports/update', 'Backend\SupportController@update')->name('supports.update');
    Route::get('/support/disable/{id}', 'Backend\SupportController@disable');
    Route::get('/support/enable/{id}', 'Backend\SupportController@enable');



    Route::get('/available_topics', 'Backend\TopicController@availableTopics')->name('available_topics.list');
    Route::post('/indexing_topics', 'Backend\TopicController@sortbyIndex');
    Route::get('/available_topics/delete/{id}', 'Backend\TopicController@availableTopicsDelete');
    Route::post('/available_topics/add', 'Backend\TopicController@availableTopicsAdd')->name('available_topics.add');
    Route::post('/available_topics/update', 'Backend\TopicController@availableTopicsUpdate')->name('available_topics.update');

    Route::get('/groups', 'Backend\GroupController@index')->name('group.list');
    Route::post('/indexing_group', 'Backend\GroupController@sortbyIndex');
    Route::get('/group/delete/{id}', 'Backend\GroupController@delete');
    Route::post('/group/add', 'Backend\GroupController@store')->name('group.add');
    Route::post('/group/update', 'Backend\GroupController@update')->name('group.update');
	
	Route::get('/peer-groups', 'Backend\PeerGroupController@index')->name('peergroup.list');
    Route::post('/indexing_peergroup', 'Backend\PeerGroupController@sortbyIndex');
    Route::get('/peer-group/delete/{id}', 'Backend\PeerGroupController@delete');
    Route::post('/peer-group/add', 'Backend\PeerGroupController@store')->name('peergroup.add');
    Route::post('/peer-group/update', 'Backend\PeerGroupController@update')->name('peergroup.update');


    Route::get('/categories', 'Backend\CategoryController@index')->name('categories.list');
    Route::get('/categories/delete/{id}', 'Backend\CategoryController@delete');
    Route::post('/categories/add', 'Backend\CategoryController@store')->name('categories.add');
    Route::post('/categories/update', 'Backend\CategoryController@update')->name('categories.update');

    Route::get('/justmental_categories', 'Backend\CategoryController@podcastCategoryindex')->name('justmental_categories.list');
    Route::post('/indexing_justmental_categories', 'Backend\CategoryController@sortbyIndex');
    Route::get('/justmental_categories/delete/{id}', 'Backend\CategoryController@podcastCategorydelete');
    Route::post('/justmental_categories/add', 'Backend\CategoryController@podcastCategorystore')->name('justmental_categories.add');
    Route::post('/justmental_categories/update', 'Backend\CategoryController@podcastCategoryupdate')->name('justmental_categories.update');

    Route::get('/mentalhealth', 'Backend\CategoryController@mIndex')->name('mentalhealth.list');
    Route::get('/mentalhealth/delete/{id}', 'Backend\CategoryController@mDelete');
    Route::post('/mentalhealth/add', 'Backend\CategoryController@mStore')->name('mentalhealth.add');
    Route::post('/mentalhealth/update', 'Backend\CategoryController@mUpdate')->name('mentalhealth.update');

    //justmental podcast
    Route::get('/justmental_podcast', 'Backend\CategoryController@podcastIndex')->name('justmental_podcast.list');
    Route::get('/justmental_podcast/delete/{id}', 'Backend\CategoryController@podcastDelete');
    Route::post('/justmental_podcast/add', 'Backend\CategoryController@podcastStore')->name('justmental_podcast.add');
    Route::post('/justmental_podcast/update', 'Backend\CategoryController@podcastUpdate')->name('justmental_podcast.update');

    // Journals
    Route::get('/journals', 'Backend\JournalController@index')->name('journals.list');
    Route::get('/journal/{id}', 'Backend\JournalController@view')->name('journals.view');
    Route::get('/journal/delete/{id}', 'Backend\JournalController@delete');
    Route::get('/journal/disable/{id}', 'Backend\JournalController@disable');
    Route::get('/journal/enable/{id}', 'Backend\JournalController@enable');

    // Weekly Vent //

    //Titles
    Route::get('/weekly-vent-titles', 'Backend\WeeklyVentController@indexTitles')->name('weekly_vent.titles.list');
    Route::get('/weekly-vent/titles/delete/{id}', 'Backend\WeeklyVentController@deleteTitle');
    Route::post('/weekly-vent/title/add', 'Backend\WeeklyVentController@storeTitle')->name('weekly_vent.title.add');
    Route::post('/weekly-vent/title/update', 'Backend\WeeklyVentController@updateTitle')->name('weekly-vent.title.update');

    //Questions
    Route::get('/weekly-vent-questions', 'Backend\WeeklyVentController@indexQuestions')->name('weekly_vent.questions.list');
    Route::get('/weekly-vent/question/delete/{id}', 'Backend\WeeklyVentController@deleteQuestion');
    Route::post('/weekly-vent/question/add', 'Backend\WeeklyVentController@storeQuestion')->name('weekly_vent.question.add');
    Route::post('/weekly-vent/question/update', 'Backend\WeeklyVentController@updateQuestion')->name('weekly_vent.question.update');
    Route::get('/weekly-vent/question/disable/{id}', 'Backend\WeeklyVentController@disableQuestion');
    Route::get('/weekly-vent/question/enable/{id}', 'Backend\WeeklyVentController@enableQuestion');
    Route::get('/weekly-vent-answers', 'Backend\WeeklyVentController@SubmittedAnswerUsersList')->name('weekly_vent.answers.list');
    Route::get('/weekly-vent/user-answers/view/{id}', 'Backend\WeeklyVentController@SubmittedAnswerView')->name('weekly_vent.answers.view');

    //Ads
    Route::get('/advertisement', 'Backend\SupportController@advertisement')->name('advertisement.list');
    Route::get('/advertisement/create', 'Backend\SupportController@createAd')->name('advertisement.create');
    Route::post('/advertisement/create', 'Backend\SupportController@storeAd')->name('advertisement.create');
    Route::get('/advertisement/edit/{id}', 'Backend\SupportController@editAd')->name('advertisement.edit');
    Route::post('/advertisement/update', 'Backend\SupportController@updateAd')->name('advertisement.update');
    Route::get('/advertisement/disable/{id}', 'Backend\SupportController@disableAd');
    Route::get('/advertisement/enable/{id}', 'Backend\SupportController@enableAd');
    Route::get('/advertisement/delete/{id}', 'Backend\SupportController@deleteAd');
    Route::get('/advertisement/{id}', 'Backend\SupportController@viewAd');

    // Reports
    Route::get('/reports', 'Backend\UserController@reports')->name('reports.list');


    Route::get('/locations', 'Backend\LocationController@index')->name('locations.list');
    Route::get('/locations/delete/{id}', 'Backend\LocationController@delete');
    Route::post('/locations/add', 'Backend\LocationController@store')->name('locations.add');
    Route::post('/locations/update', 'Backend\LocationController@update')->name('locations.update');
    
});

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


Route::get('/partner-login', function () {
    return view('partner-login');
})->name('partner-login');

Route::post('/partner-login', 'Backend\PartnerController@login');

Route::get('/partner-register', function () {
	return abort(404);
   return view('partner-register');
});
Route::post('/partner-register', 'Backend\PartnerController@register');
Route::get('/partner-cron', 'Backend\PartnerController@cron');

Route::get('/partner-password', function () {
    return view('partner-forgot-password');
});

Route::group(['middleware' => ['auth:partner']], function () { 
    Route::get('/partner-billing', 'Backend\PartnerController@getBilling');
    Route::post('/partner-billing', 'Backend\PartnerController@postBilling');


    Route::get('/partner-account', 'Backend\PartnerController@getAccount');
    Route::post('/partner-account', 'Backend\PartnerController@postAccount');

    Route::get('/partner-profile', 'Backend\PartnerController@getProfile');
    Route::post('/partner-profile', 'Backend\PartnerController@postProfile');

    Route::get('/partner-analytics', 'Backend\PartnerController@getAnalytics');
    Route::get('/partner-logout', 'Backend\PartnerController@logout');
});
