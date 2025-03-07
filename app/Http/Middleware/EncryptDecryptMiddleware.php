<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Auth;

class EncryptDecryptMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Get the authenticated user
        $user = Auth::user();
        if (!$user || !$user->encryption_key) {
            return response()->json(['message' => 'Unauthorized - Encryption key missing'], 403);
        }

        // Encrypt incoming request data for POST & PUT methods
        if ($request->isMethod('post') || $request->isMethod('put')) {
            $request->merge($this->encryptData($request->all(), $user->encryption_key));
        }

        // Get the response from the next middleware
        $response = $next($request);

        // Decrypt response data before sending it back
        return $this->decryptResponse($response, $user->encryption_key);
    }

    private function encryptData($data, $key)
    {
        return array_map(function ($value) use ($key) {
            return Crypt::encryptString($value);
        }, $data);
    }

    private function decryptResponse($response, $key)
    {
        $content = $response->getContent();
        if ($this->isJson($content)) {
            $data = json_decode($content, true);
            $decryptedData = array_map(function ($value) use ($key) {
                return Crypt::decryptString($value);
            }, $data);
            $response->setContent(json_encode($decryptedData));
        }

        return $response;
    }

    private function isJson($content)
    {
        json_decode($content);
        return (json_last_error() == JSON_ERROR_NONE);
    }
}


