<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\GraphController;
use App\Http\Controllers\DashboardController;

use Illuminate\Support\Facades\Storage;

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

/*
Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');
*/

// https://laravel.com/docs/8.x/csrf#preventing-csrf-requests
// Route::get('/token', function (Request $request) {
//     $token = $request->session()->token();
//     $token = csrf_token();
// });


Route::group(['prefix' => 'dashboard', 'as' => 'dashboard.', 'middleware' => 'auth'], function ($route) {
    // return view('dashboard');

    Route::get('/', [DashboardController::class, 'index'])->name('index');

    Route::group(['prefix' => 'graph', 'as' => 'graph.'], function ($route) {

        Route::get('/example/is-tdm-allowed.json', function () {
            return Storage::download('graph_examples/example-graph-is-tdm-allowed.json');
        })->name('example.is-tdm-allowed');

        Route::get('/', [GraphController::class, 'index'])->name('index');
        Route::get('json', [GraphController::class, 'index_json'])->name('index.json');

        Route::get('/create', [GraphController::class, 'create'])->name('create');

        Route::get('/import_form', [GraphController::class, 'get'])->name('import_form');
        Route::post('/import', [GraphController::class, 'import'])->name('import');

        Route::get('/{graph}/edit', [GraphController::class, 'edit'])->name('edit');
        Route::get('/{graph}/get', [GraphController::class, 'get'])->name('get');

        Route::post('/', [GraphController::class, 'store'])->name('store');
        Route::put('/{graph}', [GraphController::class, 'update'])->name('update');

        Route::delete('/{graph}', [GraphController::class, 'destroy'])->name('delete');

        Route::get('/{graph}/export', [GraphController::class, 'export'])->name('export');

    });

});

Route::get('/', [HomeController::class, 'index']);

Route::group(['prefix' => 'graph', 'as' => 'graph.'], function ($route) {

    Route::get('/', [GraphController::class, 'index'])->name('index');
    Route::get('/{graph}', [GraphController::class, 'show'])->name('show');
    Route::get('/{graph}/get', [GraphController::class, 'get'])->name('get');

});

// Route::resource('graph','graph' );

//
// # Panel / Back-end
// Route::get('/panel', 'PanelController@index');
// Route::get('/panel/graphs', 'GraphController@panel_index');
// Route::get('/panel/graphs/{graph}', 'GraphController@edit');
//
// # Public / Front-end
// Route::get('/', 'HomeController@index');
// Route::get('/graphs/', 'GraphController@index');


require __DIR__.'/auth.php';
