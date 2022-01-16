<?php
/**
 * Route front
 */

$suffix = sc_config('SUFFIX_URL')??'';
$prefixFaq = sc_config('PREFIX_FAQ')??'faq';
$prefixFaqCategory = sc_config('PREFIX_FAQ_CATEGORY')??'category';
$prefixFaqEntry = sc_config('PREFIX_FAQ_ENTRY')??'question';

if(sc_config('Faq')) {
    Route::group(
        [
            'namespace' => 'App\Plugins\Cms\Faq\Controllers',
            'prefix' => $prefixFaq,
        ], function () use($suffix, $prefixFaqCategory, $prefixFaqEntry) {
            Route::get('', 'FaqController@list')
                ->name('faq.list');
            Route::get('/'.$prefixFaqCategory.'/{alias}', 'FaqController@category')
            ->name('faq.category');
            Route::get('/'.$prefixFaqEntry.'/{alias}'.$suffix, 'FaqController@content')
                ->name('faq.content');
        });
}


/**
 * Route admin
 */
Route::group(
    [
        'prefix' => SC_ADMIN_PREFIX,
        'middleware' => SC_ADMIN_MIDDLEWARE,
        'namespace' => 'App\Plugins\Cms\Faq\Admin',
    ], 
    function () {
        Route::group(['prefix' => 'faq_category'], function () {
            Route::get('/', 'FaqCategoryController@index')
                ->name('admin_faq_category.index');
            Route::get('create', 'FaqCategoryController@create')
                ->name('admin_faq_category.create');
            Route::post('/create', 'FaqCategoryController@postCreate')
                ->name('admin_faq_category.create');
            Route::get('/edit/{id}', 'FaqCategoryController@edit')
                ->name('admin_faq_category.edit');
            Route::post('/edit/{id}', 'FaqCategoryController@postEdit')
                ->name('admin_faq_category.edit');
            Route::post('/delete', 'FaqCategoryController@deleteList')
                ->name('admin_faq_category.delete');
        });

        Route::group(['prefix' => 'faq_content'], function () {
            Route::get('/', 'FaqContentController@index')
                ->name('admin_faq_content.index');
            Route::get('create', 'FaqContentController@create')
                ->name('admin_faq_content.create');
            Route::post('/create', 'FaqContentController@postCreate')
                ->name('admin_faq_content.create');
            Route::get('/edit/{id}', 'FaqContentController@edit')
                ->name('admin_faq_content.edit');
            Route::post('/edit/{id}', 'FaqContentController@postEdit')
                ->name('admin_faq_content.edit');
            Route::post('/delete', 'FaqContentController@deleteList')
                ->name('admin_faq_content.delete');
        });

    }
);

