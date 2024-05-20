<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('login', 'API\UserController@login');
Route::post('register', 'API\UserController@register');

Route::post('reset-otp', 'API\UserController@resetOtp');
Route::post('reset-password', 'API\UserController@resetPassword');
Route::get('sendchimp', 'API\UserController@sendchimp');
//Partners
Route::get('questions', 'API\UserController@questions');

Route::group(['middleware' => 'auth:api'], function(){
	Route::get('details', 'API\UserController@details');
	Route::post('profile-update', 'API\UserController@profileUpdate');
	
	Route::post('submit-questions', 'API\UserController@submitQuestions');
	Route::get('deactivate', 'API\UserController@deactivate');

	// Journals
	Route::post('submit-journal', 'API\JournalController@submit');
	Route::get('journals', 'API\JournalController@journals');
	Route::post('browse-journal', 'API\JournalController@browse');
	Route::post('submit-relate-following', 'API\JournalController@addCount');
	Route::post('delete-journal', 'API\JournalController@delete');
	Route::get('test', 'API\JournalController@test');
	
	// Comments	
	Route::post('submit-comments', 'API\JournalController@submitComment');
	Route::post('comment-reply', 'API\JournalController@commentReply');
	Route::post('comments-list', 'API\JournalController@commentList');
	Route::post('replies-list', 'API\JournalController@repliesList');

	 Route::get('partners-list', 'API\PartnerController@index');
     Route::post('view-partner', 'API\PartnerController@view');
	// Topics
	Route::post('topics', 'API\TopicController@topics');
	Route::post('add-topic', 'API\TopicController@addTopic');

	Route::post('supports', 'API\TopicController@supports');
	Route::get('available-topics', 'API\TopicController@availableTopics');
	Route::post('mentalhealth', 'API\TopicController@mentalhealth');

	Route::post('topics-new', 'API\TopicController@topicsNew');
	Route::get('topic/{id}', 'API\TopicController@topic');
	Route::post('add-topic-new', 'API\TopicController@addTopicNew');
	
	
	// Message
	Route::post('send-message', 'API\UserController@sendMessage');
	Route::get('messages', 'API\UserController@messages');
	Route::get('message/{id}', 'API\UserController@message');
	// Groups
	Route::post('group', 'API\UserController@addGroup');

	// Notifications
	Route::post('send-notification', 'API\UserController@sendNotification');
	Route::get('notifications-list', 'API\UserController@listNotification');

	Route::get('resources', 'API\UserController@resources');

	Route::post('report-submit', 'API\UserController@reportSubmit');

	Route::get('faqs', 'API\UserController@faqs');

	Route::post('block-user', 'API\UserController@blockUser');

});
