<?php

return [
    'required_claims' => [ 'iss', 'iat', 'nbf', 'sub', 'jti'],
    'ttl' => env('JWT_TTL'),
    'secret' => env('JWT_SECRET'),
    'passphrase' => env('JWT_SECRET')
];