<?php

namespace App\Actions\Fortify;

use Illuminate\Validation\Rules\Password;

trait PasswordValidationRules
{
    /**
     * Retorna as regras de validação usadas para senhas.
     *
     * @return array<int, \Illuminate\Contracts\Validation\Rule|array<mixed>|string>
     */
    protected function passwordRules(): array
    {
        return [
            'required',           // Campo obrigatório
            'string',             // Deve ser uma string
            Password::default(),  // Usa as regras padrão do Laravel para senhas fortes
            'confirmed'           // Deve ter campo de confirmação (password_confirmation) igual
        ];
    }
}
