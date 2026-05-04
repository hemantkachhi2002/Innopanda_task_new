<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;


class DigitalMediaCreator
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if(user()->user_role=='digitalmedia')
        {
         return redirect()->to(route('socialmedia.dashboard'));
        }
        else
        {
         return response("You are not authorized to access route");
        }
 
        
        return $next($request);
    
    }
}
