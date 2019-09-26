<?php
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

Route::get('/clear-cache-config', function() {
    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    echo 'Cache & Config succussfully cleared.';
    exit();
});

Route::get('/config-cache', function() {
    $exitCode = Artisan::call('config:cache');
    echo 'Config succussfully cached.';
    exit();
});

Route::group(
[
    'prefix' => LaravelLocalization::setLocale(),
    'middleware' => [ 'localeSessionRedirect', 'localizationRedirect', 'localeViewPath' ]
],
function()
{
    Auth::routes();

    Route::get('/', 'user\FrontendController@index')->name('home');

    Route::post('/user/logout', 'Auth\LoginController@logoutUser')->name('user.logout');


    Route::get('/authenicated-user', function () {
        return view('user.authenicated-user');
    })->name('authenicated-user')->middleware('auth');
    
    Route::match(['get','put'], '/edit-profile/{id}', 'user\FrontendController@editProfile')->middleware('auth');
    Route::match(['get','post'], '/send-survey', 'user\FrontendController@sendSurvey')->middleware('auth');
    Route::match(['get','post'], '/take-survey', 'user\FrontendController@takeSurvey')->middleware('auth');
    Route::match(['get','post'], '/stakeholder-instance-summary', 'user\FrontendController@stakeholderInstanceSummary')->middleware('auth');
    Route::get('/get-stakeholder-comments/{id}/{survey_id}', 'user\FrontendController@getStakeholderComments')->middleware('auth');
    Route::match(['get','post'], '/stakeholder-welcome/{hash}', 'user\FrontendController@stakeholderWelcome');
    Route::match(['get','post'], '/stakeholder', 'user\FrontendController@stakeholder');
    Route::match(['get','post'], '/stakeholder-engagement', 'user\FrontendController@stakeholderEngagement');
    Route::get('/thank-you', function () {
        return view('user.thank-you');
    });
    Route::get('/completed-survey', function () {
        return view('user.completed-survey');
    });
});

Route::group(['prefix' => 'admin'], function(){
    
    Route::get('/', 'admin\AdminController@dashboard')->name('admin.home');

	Route::get('/login', 'AuthAdmin\LoginController@showLoginForm')->name('admin.login');
	Route::post('/login', 'AuthAdmin\LoginController@login')->name('admin.login.submit');
	Route::post('/logout', 'AuthAdmin\LoginController@logout')->name('admin.logout');
	Route::get('/password/reset', 'AuthAdmin\ForgotPasswordController@showLinkRequestForm')->name('admin.password.request');
    Route::post('/password/email', 'AuthAdmin\ForgotPasswordController@sendResetLinkEmail')->name('admin.password.email');
    Route::get('/password/reset/{token}', 'AuthAdmin\ResetPasswordController@showResetForm')->name('admin.password.reset');
    Route::post('/password/reset', 'AuthAdmin\ResetPasswordController@reset');

    Route::resource('admins', 'admin\AdminController');
    Route::get('get-admins', 'admin\AdminController@getAdmins')->name('get-admins');
    
    Route::resource('users', 'admin\UserController');
    Route::get('get-users', 'admin\UserController@getUsers')->name('get-users');
    
    Route::resource('companies', 'admin\CompanyController');
    Route::get('get-companies', 'admin\CompanyController@getUsers')->name('get-companies');

    Route::resource('stakeholders', 'admin\StakeholderController');
    Route::get('get-stakeholders', 'admin\StakeholderController@getStakeholdes')->name('get-stakeholders');

    Route::resource('esg', 'admin\ESGController');
    Route::get('get-esg', 'admin\ESGController@getEsg')->name('get-esg');

    Route::resource('email-templates', 'admin\EmailTemplateController');
    Route::get('get-email-templates', 'admin\EmailTemplateController@getEmailTemplates')->name('get-email-templates');

    Route::resource('surveys', 'admin\SurveyController');
    Route::get('get-surveys', 'admin\SurveyController@getSurveys')->name('get-surveys');

    //Route::resource('query', 'admin\SurveyEntryController');
    
    Route::get('survey_entries', 'admin\SurveyEntryController@index');
    Route::get('get-survey-entries', 'admin\SurveyEntryController@getSurveyEntries')->name('get-survey-entries');

    Route::get('survey-report', 'admin\SurveyEntryController@surveyReport')->name('survey-report');
    Route::get('get-survey-report', 'admin\SurveyEntryController@getSurveyReport')->name('get-survey-report');
    Route::match(['get','post'], 'all-companies-survey-report', 'admin\SurveyEntryController@allCompaniesSurveyReport')->name('all-companies-survey-report');
    Route::match(['get','post'], 'individual-company-survey-report', 'admin\SurveyEntryController@individualCompanySurveyReport')->name('individual-company-survey-report');
    //Route::post('get-individual-company-survey-report', 'admin\SurveyEntryController@getindividualCompanySurveyReport')->name('get-individual-company-survey-report');
    Route::get('filter-drop-down', 'admin\SurveyEntryController@filterDropDown')->name('filter-drop-down');
	Route::get('/get-stakeholder-comments/{id}/{survey_id}', 'admin\SurveyEntryController@getStakeholderComments');
    Route::get('export-file/{id}/{type}', 'admin\SurveyEntryController@exportFile')->name('export.file');
});
