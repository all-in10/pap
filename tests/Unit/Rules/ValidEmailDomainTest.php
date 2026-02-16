<?php

use App\Rules\ValidEmailDomain;
use Illuminate\Support\Facades\Validator;

it('validates valid emails', function () {
    $rule = new ValidEmailDomain();
    
    // Testes com emails válidos
    expect($rule->passes('email', 'teste@empresa.com'))->toBeTrue();
    expect($rule->passes('email', 'user@domain.pt'))->toBeTrue();
    expect($rule->passes('email', 'admin@sub.domain.co.uk'))->toBeTrue();
    expect($rule->passes('email', 'test.user@example.org'))->toBeTrue();
});

it('rejects invalid emails without domain extension', function () {
    $rule = new ValidEmailDomain();
    
    // Testes com emails inválidos (sem TLD)
    expect($rule->passes('email', 'teste@teste'))->toBeFalse();
    expect($rule->passes('email', 'user@localhost'))->toBeFalse();
    expect($rule->passes('email', 'admin@company'))->toBeFalse();
});

it('rejects emails without domain', function () {
    $rule = new ValidEmailDomain();
    
    // Testes com emails malformatados
    expect($rule->passes('email', 'invalido@'))->toBeFalse();
    expect($rule->passes('email', '@domain.com'))->toBeFalse();
    expect($rule->passes('email', 'nodomain'))->toBeFalse();
});

it('returns correct validation message', function () {
    $rule = new ValidEmailDomain();
    
    expect($rule->message())->toContain('domínio com extensão válida');
});
