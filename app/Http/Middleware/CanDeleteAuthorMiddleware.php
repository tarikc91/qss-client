<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Repositories\Contracts\AuthorRepository;

class CanDeleteAuthorMiddleware
{
    /**
     * Constructor
     *
     * @param AuthorRepository $authorRepository
     */
    public function __construct(private AuthorRepository $authorRepository) {}

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $author = $this->authorRepository->find($request->route()->author);

        if(!$author?->eligibleForDelete()) {
            return redirect()
                ->route('authors.index')
                ->with('error', 'Author can not be deleted.');
        }

        return $next($request);
    }
}
