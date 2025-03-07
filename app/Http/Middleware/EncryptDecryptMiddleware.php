<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Crypt;

class EncryptDecryptMiddleware
{
    public function handle($request, Closure $next)
    {
        // Encrypt request data before passing it to the controller
        if ($request->has('data')) {
            $request->merge(['data' => Crypt::encryptString($request->data)]);
        }

        // Process the request
        $response = $next($request);

        // Decrypt the response data
        $responseContent = $response->getContent();
        $decryptedResponse = Crypt::decryptString($responseContent);

        $response->setContent($decryptedResponse);
        
        return $response;
    }
}

