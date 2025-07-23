<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsTeacher
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // For now, check if user email ends with .edu or has a teacher role
        // In production, you would check a proper role/permission system
        $user = $request->user();
        
        if (!$user) {
            return redirect()->route('login');
        }
        
        // Check if user has teacher role (you would add this column to users table)
        // For demo purposes, we'll check if email ends with .edu
        if (str_ends_with($user->email, '.edu') || $user->email === 'teacher@example.com') {
            return $next($request);
        }
        
        return redirect()->route('learning.index')
            ->with('error', 'You do not have permission to access the teacher dashboard.');
    }
}
