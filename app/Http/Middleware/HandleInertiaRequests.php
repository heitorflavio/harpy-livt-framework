<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\File;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {

        $file = lang_path(App::currentLocale() . ".json");

        return array_merge(parent::share($request), [
            'csrf' => csrf_token(),
            'locale' => App::currentLocale(),
            'locales' => config('app.available_locales'),
            'translations' => File::exists($file) ? File::json($file) : [],
            'auth' => [
                'user' => $request->user(),
            ]
        ]);
    }
}
