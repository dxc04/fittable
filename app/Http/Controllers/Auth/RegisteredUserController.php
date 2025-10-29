<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    /**
     * Show the registration page.
     */
    public function create(Request $request): Response
    {
        $registrationRole = $request->session()->get('registration_role', 'job_seeker');

        // Only lock role selection if there's pending job analysis or recruiter match
        $roleFromSession = $request->session()->has('pending_job_analysis')
            || $request->session()->has('pending_recruiter_match');

        return Inertia::render('auth/Register', [
            'registrationRole' => $registrationRole,
            'roleFromSession' => $roleFromSession,
        ]);
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        try {
            \Log::info('Registration attempt', ['email' => $request->email]);

            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
                'role' => 'required|in:job_seeker,recruiter',
            ]);

            \Log::info('Validation passed');

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            \Log::info('User created', ['user_id' => $user->id]);

            // Assign role from form input (with session fallback for safety)
            $role = $request->input('role', $request->session()->get('registration_role', 'job_seeker'));
            $user->assignRole($role);

            \Log::info('Role assigned', ['user_id' => $user->id, 'role' => $role]);

            event(new Registered($user));

            \Log::info('Registered event fired');

            Auth::login($user);

            \Log::info('User logged in');

            $request->session()->regenerate();

            // Check if there's a pending job analysis to process
            if ($request->session()->has('pending_job_analysis')) {
                \Log::info('Redirecting to pending job analysis');

                return redirect()->route('job.analyze.process');
            }

            // Check if there's a pending recruiter match to process
            if ($request->session()->has('pending_recruiter_match')) {
                \Log::info('Redirecting to pending recruiter match');

                return redirect()->route('recruiter.match.process');
            }

            // Default redirect based on role
            if ($user->hasRole('recruiter')) {
                \Log::info('Redirecting to recruiter.index');

                return redirect()->route('recruiter.index');
            }

            \Log::info('Redirecting to job.analyze');

            return to_route('job.analyze');
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Registration validation failed', [
                'error' => $e->getMessage(),
            ]);

            throw $e;
        } catch (\Exception $e) {
            \Log::error('Registration failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }
}
