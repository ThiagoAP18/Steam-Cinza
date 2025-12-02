<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Contracts\Auth\MustVerifyEmail; // Interface para usuários que precisam verificar e-mail
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\UpdatesUserProfileInformation;

class UpdateUserProfileInformation implements UpdatesUserProfileInformation
{
    /**
     * Valida e atualiza as informações de perfil do usuário fornecido.
     *
     * @param  array<string, mixed>  $input  Dados enviados pelo formulário (nome, email, foto)
     */
    public function update(User $user, array $input): void
    {
        // Validação dos dados
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'], // Nome obrigatório, tipo string, máximo 255 caracteres
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)], // Email obrigatório, único no banco exceto o do próprio usuário
            'photo' => ['nullable', 'mimes:jpg,jpeg,png', 'max:1024'], // Foto opcional, somente jpg/jpeg/png, até 1MB
        ])->validateWithBag('updateProfileInformation'); // Validação usando um "bag" específico para perfil

        // Se houver foto no input, atualiza a foto do perfil
        if (isset($input['photo'])) {
            $user->updateProfilePhoto($input['photo']);
        }

        // Se o email foi alterado e o usuário precisa verificar email
        if ($input['email'] !== $user->email &&
            $user instanceof MustVerifyEmail) {
            $this->updateVerifiedUser($user, $input); // Atualiza email e reinicia verificação
        } else {
            // Caso contrário, apenas atualiza nome e email normalmente
            $user->forceFill([
                'name' => $input['name'],
                'email' => $input['email'],
            ])->save();
        }
    }

    /**
     * Atualiza as informações de um usuário que precisa verificar o email.
     *
     * @param  array<string, string>  $input  Dados enviados pelo formulário
     */
    protected function updateVerifiedUser(User $user, array $input): void
    {
        $user->forceFill([
            'name' => $input['name'],
            'email' => $input['email'],
            'email_verified_at' => null, // Reseta a verificação de email
        ])->save();

        $user->sendEmailVerificationNotification(); // Envia notificação de verificação de email
    }
}
