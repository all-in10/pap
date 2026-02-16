<?php

use App\Rules\ValidEmailDomain;
use Illuminate\Support\Facades\Validator;

it('validates email using Laravel validator with custom rule', function () {
    // Teste 1: Email inválido sem extensão de domínio
    $validator = Validator::make(
        ['email' => 'teste@teste'],
        ['email' => ['required', new ValidEmailDomain()]]
    );
    
    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->has('email'))->toBeTrue();
});

it('accepts valid email with domain extension', function () {
    // Teste 2: Email válido com extensão de domínio
    $validator = Validator::make(
        ['email' => 'joao@empresa.com'],
        ['email' => ['required', new ValidEmailDomain()]]
    );
    
    expect($validator->passes())->toBeTrue();
    expect($validator->errors()->has('email'))->toBeFalse();
});

it('rejects emails with only local and domain without TLD', function () {
    // Teste 3: Rejeita emails malformados
    $invalidEmails = [
        'user@localhost',
        'admin@company',
        'test@domain',
        'invalid@',
        '@domain.com',
    ];

    foreach ($invalidEmails as $email) {
        $validator = Validator::make(
            ['email' => $email],
            ['email' => ['required', new ValidEmailDomain()]]
        );
        
        expect($validator->fails())->toBeTrue(
            "Email '{$email}' deveria ser inválido"
        );
    }
});

it('accepts valid international domain extensions', function () {
    // Teste 4: Aceita várias extensões válidas
    $validEmails = [
        'usuario@empresa.com',
        'user@domain.pt',
        'admin@company.co.uk',
        'test@site.org',
        'name@example.net',
        'mail@subdomain.company.com',
    ];

    foreach ($validEmails as $email) {
        $validator = Validator::make(
            ['email' => $email],
            ['email' => ['required', new ValidEmailDomain()]]
        );
        
        expect($validator->passes())->toBeTrue(
            "Email '{$email}' deveria ser válido"
        );
    }
});
