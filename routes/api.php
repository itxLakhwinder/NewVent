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
Route::post('reset-password-update', 'API\UserController@resetPasswordUpdate');
Route::get('sendchimp', 'API\UserController@sendchimp');
Route::get('ptest', 'API\JournalController@ptest');
Route::get("/app-settings", 'API\UserController@app_settings');

Route::group(['middleware' => 'auth:api'], function(){
	Route::get('details', 'API\UserController@details');
	Route::post('profile-update', 'API\UserController@profileUpdate');
	Route::get('questions', 'API\UserController@questions');
	Route::post('submit-questions', 'API\UserController@submitQuestions');
	Route::get('deactivate', 'API\UserController@deactivate');
	
	//New Api
	
	Route::get('deactivate/new', 'API\UserController@newDeactivate');

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
	Route::post('comment-edit', 'API\JournalController@commentEdit');
	Route::post('comment-delete', 'API\JournalController@commentDelete');
	Route::post('podcast-comment-delete', 'API\JournalController@podcastcommentDelete');
	Route::post('reply-delete', 'API\JournalController@replyDelete');
	Route::post('reply-edit', 'API\JournalController@replyEdit');
	Route::post('comments-list', 'API\JournalController@commentList');
	Route::post('replies-list', 'API\JournalController@repliesList');

    //Partners
	Route::post('partners-list', 'API\PartnerController@index');
    Route::post('view-partner', 'API\PartnerController@view');
	Route::post('partner-count', 'API\PartnerController@count');
	Route::post('mental-count', 'API\JournalController@count');
	Route::post('justmental-count', 'API\JournalController@justMentalCount');

	
	// Topics
	Route::post('topics', 'API\TopicController@topics');
	Route::post('add-topic', 'API\TopicController@addTopic');
	
	Route::post('topics/v1', 'API\TopicController@topicsNew');
	Route::post('add-topic/v1', 'API\TopicController@addTopicNew');
	
	Route::post('supports', 'API\TopicController@supports');
	Route::get('available-topics', 'API\TopicController@availableTopics');
	Route::post('mentalhealth', 'API\TopicController@mentalhealth');
	Route::post('justmental_podcast', 'API\TopicController@justmental_podcast');

	Route::post('topics-new', 'API\TopicController@topicsNew');
	Route::post('stories', 'API\TopicController@stories');
	Route::post('group-stories', 'API\TopicController@groupStories');
	Route::post('users-stories', 'API\TopicController@userStories');
	Route::get('topic/{id}', 'API\TopicController@topic');
	Route::post('add-topic-new', 'API\TopicController@addTopicNew');
	Route::post('block-topic', 'API\TopicController@blockTopics');
	Route::post('unblock-topic', 'API\TopicController@unblockTopics');
	Route::get('blocked-topics', 'API\TopicController@BlockedTopicsList');
	
	
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
	Route::post('log-user', 'API\UserController@logUser');

	Route::get('locations', 'API\UserController@locations');

	//Weekly Vent
	Route::get('weekly-vent-questions', 'API\WeeklyVentController@GetWeeklyVentQuestions');
	Route::post('weekly-vent/submit-answers', 'API\WeeklyVentController@submitWeeklyVentQuestions');
	
	//advertisement
	Route::get('advertisements', 'API\AdvertisementController@advertisements'); 
	Route::get('advertisement/{id}', 'API\AdvertisementController@advertisement');
	
	//Groups
	Route::get('group-list', 'API\GroupController@index');
	Route::post('join-unjoin', 'API\GroupController@joinUnjoinStatus');
	Route::post('story-post', 'API\GroupController@listStoryPost');
	Route::post('topics/v2', 'API\GroupController@peerGroupPost');
	Route::post('users-stories/v2', 'API\GroupController@peerUserStories');
	Route::post('add-topic/v2', 'API\GroupController@peerAddTopic');
	
	Route::get('notification-setting-list', 'API\UserController@getNotificationSetting');
	Route::post('notification-setting', 'API\UserController@notificationSetting');
	
});
