<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $user = $request->user();
        $employee = $user ? $user->employee : null;
        $firstNameOnly = null;
        if ($employee && $employee->name_en) {
            $nameParts = explode(' ', trim($employee->name_en));
            $firstNameOnly = end($nameParts);
        }

        return array_merge(parent::share($request), [
            'auth' => [
                'user' => $request->user() ? [
                    'id' => $request->user()->id,
                    'username' => $user->username,
                    'email' => $user->email,
                    'name' => $request->user()->name,
                    'first_letter' => mb_substr($firstNameOnly, 0, 1),
                    'name_en' => $firstNameOnly,
                    'position' => [
                        'name' => $employee && $employee->position ? $employee->position->name : 'N/A',
                    ],
                    'role' => $request->user()->role,
                ] : null,
                'notifications' => $request->user() ? $request->user()->unreadNotifications : [],
            ],

            'errors' => function () {
                return session()->get('errors')
                    ? collect(session()->get('errors')->getBag('default')->getMessages())->map(function ($error) {
                        return $error[0];
                    })
                    : (object) [];
            },
            'flash' => [
                'success' => $request->session()->get('success'),
                'error' => $request->session()->get('error'),
            ],
        ]);
    }
}
