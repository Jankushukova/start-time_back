<?php

use Illuminate\Http\Request;

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
Route::get('email/verify/{id}', 'VerificationApiController@verify')->name('verificationapi.verify');
Route::get('email/resend', 'VerificationApiController@resend')->name('verificationapi.resend');

Route::group([
    'prefix' => '/v1', 'namespace' => 'Api\V1', 'as' => 'api.'], function ()
{
    Route::group([], function () {
            Route::namespace('User')->group(function () {

                Route::get('users/partners', 'UsersController@getPartners');//+
                Route::get('users/{id}', 'UsersController@show')->where('id', '[0-9]+');//+
                //followers
                Route::get('followers/{id}', 'FollowerController@getFollowers')->where('id', '[0-9]+');//+
                Route::get('followings/{id}', 'FollowerController@getFollowings')->where('id', '[0-9]+');//+
                //subscribe
                Route::post('subscribe', ['uses' => 'SubscribersController@store', 'as' => 'subscribe.store']);//+
                Route::get('open', 'UsersController@open');

                Route::namespace('Authentication')->group(function () {
                    Route::post('adminLogin', 'LoginController@authenticateAdmin');//+
                    Route::post('userLogin', 'LoginController@authenticateUser');//+
                    Route::post('register', 'RegistrationController@register');//+
                    Route::post('send/reset/password/link', 'ResetPasswordController@sendEmail');
                    Route::post('change/password', 'ChangePasswordController@process');
                    Route::post('auth/facebook', 'SocialAuthController@facebookAuth');
                    Route::get('redirect', 'SocialAuthController@redirect');
                    Route::get('callback', 'SocialAuthController@callback');

                    //partners
                    });

                });

        Route::namespace('Project')->group(function () {
            //project category
            Route::get('project/categories', 'ProjectCategoryController@index');//+

            //project
            Route::get('project/category', ['uses' => 'ProjectsController@getProjectsOfCategory', 'as' => 'category.projects']);//+
            Route::post('project/view/add', ['uses' => 'ProjectsController@addView', 'as' => 'project.view']);//+
            Route::get('project/{id}', ['uses' => 'ProjectsController@show', 'as' => 'project.show'])->where('id', '[0-9]+');//+
            //images
            Route::get('project/images/{id}', ['uses' => 'ProjectImagesController@getImagesOfProject', 'as' => 'project.images'])->where('id', '[0-9]+');//+
            //bakers
            Route::get('project/bakers/{id}', ['uses' => 'ProjectOrderController@getBakersOfProject', 'as' => 'project.bakers'])->where('id', '[0-9]+');//+
            //likes
            Route::get('project/like/{id}', ['uses' => 'ProjectLikesController@getLikesOfProject', 'as' => 'project.likes'])->where('id', '[0-9]+');//+
            //comments
            Route::get('project/comments/{id}', ['uses' => 'ProjectCommentsController@getCommentsOfProject', 'as' => 'project.comments'])->where('id', '[0-9]+');//+
            //gifts
            Route::get('project/gifts/{id}', ['uses' => 'ProjectGiftsController@getGiftsOfProject', 'as' => 'project.gifts'])->where('id', '[0-9]+');//+
            Route::get('gift/{id}', ['uses' => 'ProjectGiftsController@show', 'as' => 'gifts.show'])->where('id', '[0-9]+');//+
            //updates
            Route::get('project/updates/{id}', ['uses' => 'UpdatesController@getUpdatesOfProject', 'as' => 'project.updates'])->where('id', '[0-9]+');//+
            Route::get('updates/images/{id}', ['uses' => 'UpdatesController@getUpdatesImages', 'as' => 'updates.images'])->where('id', '[0-9]+');//+
            Route::get('update/{id}', ['uses' => 'UpdatesController@show', 'as' => 'updates.show'])->where('id', '[0-9]+');//+
            //most popular
            Route::get('project/popular', ['uses' => 'ProjectsController@getMostPopular', 'as' => 'projects.popular']);//+
            //questions
            Route::get('project/questions/{id}', ['uses' => 'ProjectQuestionController@getQuestionsOfProject', 'as' => 'project.comments'])->where('id', '[0-9]+');//+

            //guest user profile
            Route::get('user/projects', 'ProjectsController@getUserProjects');//+

            //statistics
            Route::get('statistics/project', 'ProjectsController@getAmountOfProjects');
            Route::get('statistics/project/successful', 'ProjectsController@getAmountOfSuccessfulProjects');
            Route::get('statistics/project/bakers', 'ProjectsController@getAmountOfBakers');

            //order
            Route::post('project/orders', ['uses' => 'ProjectOrderController@store', 'as' => 'order.store']);//+


        });




        Route::namespace('news')->group(function () {
            //news
            Route::get('news/all', 'NewsController@index')->where('id', '[0-9]+');//+
            Route::get('news/{id}', 'NewsController@show');//+
            Route::post('news/view/add', ['uses' => 'NewsController@addView', 'as' => 'news.view']);//+

            //images
            Route::get('news/images/{id}', ['uses' => 'NewsImagesController@getImagesOfNews', 'as' => 'news.images'])->where('id', '[0-9]+');//+
            //likes
            Route::get('news/like/{id}', ['uses' => 'NewsLikesController@getLikesOfNews', 'as' => 'news.likes'])->where('id', '[0-9]+');//+
            //comments
            Route::get('news/comment/{id}', ['uses' => 'NewsCommentsController@getCommentsOfNews', 'as' => 'news.comments'])->where('id', '[0-9]+');//+

        });


        Route::namespace('product')->group(function () {
            //shop
            Route::get('products', ['uses' => 'ProductController@index', 'as' => 'products.index']);//+
            Route::get('product/show/{id}', ['uses' => 'ProductController@show', 'as' => 'products.show'])->where('id', '[0-9]+');//+
            Route::get('product/images/{id}', ['uses' => 'ProductImageController@getImages', 'as' => 'product.images'])->where('id', '[0-9]+');//+
            Route::post('product/view/add', ['uses' => 'ProductController@addView', 'as' => 'product.view']);//+
            //most popular
            Route::get('product/popular', ['uses' => 'ProductController@getMostPopular', 'as' => 'products.popular']);//+

        });

        //general
//        Route::post('payment', ['uses' => 'PaymentController@store', 'as' => 'payment.store']);//+
        Route::get('comment/likes/{id}', ['uses' => 'CommentLikeController@getLikesOfComment', 'as' => 'comment.likes'])->where('id', '[0-9]+');//+


        Route::get('payment/basic/auth', 'PaymentController@basicAuth');//+
        Route::get('payment/success', 'PaymentController@success');//+
        Route::get('payment/fail', 'PaymentController@failure');//+
        Route::get('payment/check', 'PaymentController@checkPay');//+
        Route::get('payment/control', 'PaymentController@controlPay');//+

        Route::group(['middleware' => ['jwt.verify', 'verified']], function() {

            Route::namespace('Project')->group(function () {
                //authenticated user profile
                Route::get('user/bakers/{id}', 'ProjectOrderController@getUserBakers')->where('id', '[0-9]+');//+
                Route::get('projects/user/baked/{id}', 'ProjectOrderController@getUserBakedProjects')->where('id', '[0-9]+');//+



                // project
                Route::post('project', ['uses' => 'ProjectsController@store', 'as' => 'projects.store']);//+
                Route::post('project/create/images', ['uses' => 'ProjectImagesController@store', 'as' => 'images.store']);//+
                //gift
                Route::post('gifts', ['uses' => 'ProjectGiftsController@store', 'as' => 'gifts.store']);//+
                Route::put('gifts', ['uses' => 'ProjectGiftsController@update', 'as' => 'gifts.update']);//+
                Route::delete('gifts', ['uses' => 'ProjectGiftsController@destroy', 'as' => 'gifts.destroy']);//+
                //comments
                Route::post('project/comment', ['uses' => 'ProjectCommentsController@store', 'as' => 'projects.store']);//+
                Route::delete('project/comment/{id}', ['uses' => 'ProjectCommentsController@destroy', 'as' => 'projects.destroy'])->where('id', '[0-9]+');//+
                //updates
                Route::post('update', ['uses' => 'UpdateController@store', 'as' => 'update.store']);//+
                //likes
                Route::post('project/like', ['uses' => 'ProjectLikesController@store', 'as' => 'projects.store']);//+
                Route::delete('project/like/{id}', ['uses' => 'ProjectLikesController@destroy', 'as' => 'projects.destroy'])->where('id', '[0-9]+');//+
                //questions
                Route::post('project/questions', ['uses' => 'ProjectQuestionController@store', 'as' => 'questions.store']);//+
                Route::delete('project/questions/{id}', ['uses' => 'ProjectQuestionController@destroy', 'as' => 'questions.destroy'])->where('id', '[0-9]+');//+


            });

            Route::namespace('product')->group(function () {
                //products
                Route::post('product', ['uses' => 'ProductController@store', 'as' => 'product.store']);//+
                Route::post('product/create/images', ['uses' => 'ProductImageController@store', 'as' => 'images.store']);//+
                Route::post('product/order', ['uses' => 'ProductOrderController@store', 'as' => 'order.store']);//+
                //likes
                Route::post('product/like', ['uses' => 'ProductLikeController@store', 'as' => 'product.store']);//+
                Route::delete('product/like/{id}', ['uses' => 'ProductLikeController@destroy', 'as' => 'product.destroy'])->where('id', '[0-9]+');//+


            });
            Route::namespace('news')->group(function () {
                //news
                //comment
                Route::post('news/comment', ['uses' => 'NewsCommentsController@store', 'as' => 'news.store']);//+
                Route::delete('news/comment/{id}', ['uses' => 'NewsCommentsController@destroy', 'as' => 'news.destroy'])->where('id', '[0-9]+');//+
                //like
                Route::post('news/like', ['uses' => 'NewsLikesController@store', 'as' => 'news.store']);//+
                Route::delete('news/like/{id}', ['uses' => 'NewsLikesController@destroy', 'as' => 'news.destroy'])->where('id', '[0-9]+');//+

            });



            Route::namespace('user')->group(function () {
                // user
                Route::get('user', 'UsersController@getAuthenticatedUser');//+
                Route::get('users/profile/information', 'UsersController@UserProfileInformation');//+
                Route::get('users/recommendations', 'UsersController@userRecommendations');

                //follower
                Route::post('followers', ['uses' => 'FollowerController@store', 'as' => 'followers.store']);//+
                Route::delete('followers/{id}', ['uses' => 'FollowerController@destroy', 'as' => 'followers.store'])->where('id', '[0-9]+');//+
            });


            //payments
            Route::get('payments', ['uses' => 'PaymentController@getMyPayments', 'as' => 'my.payments']);//+

            //comment
            Route::post('comment/like', ['uses' => 'CommentLikeController@store', 'as' => 'comment.store']);//+
            Route::delete('comment/like/{id}', ['uses' => 'CommentLikeController@destroy', 'as' => 'comment.destroy'])->where('id', '[0-9]+');//+



            Route::group(['middleware' => ['admin']], function() {
                Route::namespace('Project')->group(function () {
                    //project-category
                    Route::get('project/category/all', ['uses' => 'ProjectCategoryController@getAllCategories', 'as' => 'projects.AllCategories']);//+
                    Route::post('project/category/create', ['uses' => 'ProjectCategoryController@store', 'as' => 'projects.store']);//+
                    Route::delete('project/category/delete/{id}', ['uses' => 'ProjectCategoryController@destroy', 'as' => 'projects.destroy'])->where('id', '[0-9]+');//+
                    Route::put('project/category/update/{id}', ['uses' => 'ProjectCategoryController@update', 'as' => 'projects.update'])->where('id', '[0-9]+');//+

                    // project
                    Route::get('project', ['uses' => 'ProjectsController@index', 'as' => 'projects.index'])->where('id', '[0-9]+');//+
                    Route::delete('project/{id}', ['uses' => 'ProjectsController@destroy', 'as' => 'projects.store'])->where('id', '[0-9]+');//+
                    Route::put('project/{id}', ['uses' => 'ProjectsController@update', 'as' => 'projects.update'])->where('id', '[0-9]+');//+

                    //bakers
                    Route::get('project/bakers/all', ['uses' => 'ProjectOrderController@getAllBakers', 'as' => 'projects.bakers']);//+

                    //orders
                    Route::get('project/payments/{id}', ['uses' => 'ProjectOrderController@getPaymentsOfProject', 'as' => 'project.payments'])->where('id', '[0-9]+');//+
                    Route::get('project/type/payments/{id}', ['uses' => 'ProjectOrderController@getPaymentsOfProjectOfType', 'as' => 'type.payments'])->where('id', '[0-9]+');//+

                    //updates
                    Route::put('update', ['uses' => 'UpdateController@update', 'as' => 'update.update'])->where('id', '[0-9]+');//+
                    Route::delete('update', ['uses' => 'UpdateController@destroy', 'as' => 'update.destroy'])->where('id', '[0-9]+');//+

                });

                Route::namespace('product')->group(function () {
                    // products
                    Route::get('product/all', ['uses' => 'ProductController@getAllProducts', 'as' => 'product.index'])->where('id', '[0-9]+');//+
                    Route::delete('product/{id}', ['uses' => 'ProductController@destroy', 'as' => 'product.store'])->where('id', '[0-9]+');//+
                    Route::put('product/{id}', ['uses' => 'ProductController@update', 'as' => 'product.update'])->where('id', '[0-9]+');//+

                    //order
                    Route::get('product/order/{id}', ['uses' => 'ProductOrderController@getOrdersOfProduct', 'as' => 'product.orders']);//+
                    Route::delete('product/order/{id}', ['uses' => 'ProductOrderController@destroy', 'as' => 'product.destroy'])->where('id', '[0-9]+');//+
                    Route::put('product/order/{id}', ['uses' => 'ProductOrderController@update', 'as' => 'product.update'])->where('id', '[0-9]+');//+
                    Route::get('product/order/{id}', ['uses' => 'ProductOrderController@show', 'as' => 'product.show'])->where('id', '[0-9]+');//+

                    //orders
                    Route::get('product/payments/{id}', ['uses' => 'ProductPaymentController@getPaymentsOfProduct', 'as' => 'product.payments'])->where('id', '[0-9]+');//+
                    Route::get('product/type/payments/{id}', ['uses' => 'ProductPaymentController@getPaymentsOfProductOfType', 'as' => 'type.payments'])->where('id', '[0-9]+');//+

                });


                Route::namespace('news')->group(function () {
                    //news
                    Route::post('news', ['uses' => 'NewsController@store', 'as' => 'news.store']);//+
                    Route::put('news/{id}', ['uses' => 'NewsController@update', 'as' => 'news.update'])->where('id', '[0-9]+');//+
                    Route::delete('news/{id}', ['uses' => 'NewsController@destroy', 'as' => 'news.destroy'])->where('id', '[0-9]+');//+

                });

                Route::namespace('user')->group(function () {
                    //subscribe
                    Route::get('subscribe', ['uses' => 'SubscribersController@index', 'as' => 'subscribers.index']);//+
                    Route::put('subscribe/{id}', ['uses' => 'SubscribersController@update', 'as' => 'subscribers.update'])->where('id', '[0-9]+');//+
                    Route::delete('subscribe/{id}', ['uses' => 'SubscribersController@destroy', 'as' => 'subscribers.destroy'])->where('id', '[0-9]+');//+

                });

                //payments
                Route::put('payment/{id}', ['uses' => 'PaymentController@update', 'as' => 'payments.update'])->where('id', '[0-9]+');//+
                Route::delete('payment/{id}', ['uses' => 'PaymentController@destroy', 'as' => 'payments.destroy'])->where('id', '[0-9]+');//+
                Route::get('payment/{id}', ['uses' => 'PaymentController@show', 'as' => 'payments.show'])->where('id', '[0-9]+');//+





            });
            });
        });

});

