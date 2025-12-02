<?php

namespace Database\Factories;

use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Jetstream\Features;

/**
 * @extends \Illuminate\Database\Eloquent\Factories.Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * Armazena a senha atual usada pela factory.
     * Isso evita recalcular o hash da senha várias vezes.
     */
    protected static ?string $password;

    /**
     * Define o estado padrão dos atributos do model User.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // Gera um nome aleatório
            'name' => fake()->name(),

            // Gera um e-mail seguro e único
            'email' => fake()->unique()->safeEmail(),

            // Marca o e-mail como verificado
            'email_verified_at' => now(),

            // Define a senha (hashada uma vez e reutilizada)
            'password' => static::$password ??= Hash::make('password'),

            // Desativa inicialmente o 2FA
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,

            // Token para lembrar sessão
            'remember_token' => Str::random(10),

            // Caminho para foto de perfil (nulo por padrão)
            'profile_photo_path' => null,

            // ID do time atual (Jetstream)
            'current_team_id' => null,
        ];
    }

    /**
     * Indica que o e-mail do usuário deve ser criado como "não verificado".
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Indica que o usuário deve ter um "time pessoal"
     * (recurso do Jetstream quando teams estão habilitados).
     */
    public function withPersonalTeam(?callable $callback = null): static
    {
        // Se teams não estão ativos, não faz nada
        if (! Features::hasTeamFeatures()) {
            return $this->state([]);
        }

        // Cria um time relacionado ao usuário
        return $this->has(
            Team::factory()
                ->state(fn (array $attributes, User $user) => [
                    'name' => $user->name.'\'s Team',  // Nome padrão do time
                    'user_id' => $user->id,           // Dono do time
                    'personal_team' => true,          // Marca como time pessoal
                ])
                ->when(is_callable($callback), $callback), // Permite customização opcional

            'ownedTeams' // Relacionamento que será populado
        );
    }
}
