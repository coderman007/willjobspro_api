<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Throwable;

/**
 * @method getRoleNames()
 * @property mixed $city
 * @property mixed $state
 * @property mixed $country
 * @property mixed $zipCode
 * @property mixed $id
 * @property mixed $name
 * @property mixed $email
 */
class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Inicializar las variables para evitar errores de "undefined property"
        $country = null;
        $state = null;
        $city = null;
        $zipCode = null;

        // Manejar la posibilidad de que los datos de ubicación no estén disponibles
        try {
            $country = [
                'name' => $this->country->name,
                'dial_code' => $this->country->dial_code,
                'iso_alpha_2' => $this->country->iso_alpha_2
            ];
        } catch (Throwable $th) {
            // Si no hay datos de país, se deja como null
        }

        try {
            $state = $this->state->name;
        } catch (Throwable $th) {
            // Si no hay datos de estado, se deja como null
        }

        try {
            $city = $this->city->name;
        } catch (Throwable $th) {
            // Si no hay datos de ciudad, se deja como null
        }

        try {
            $zipCode = $this->zipCode->code;
        } catch (Throwable $th) {
            // Si no hay datos de código postal, se deja como null
        }

        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->getRoleNames(),
            'location' => [
                'country' => $country,
                'state' => $state,
                'city' => $city,
                'zip_code' => $zipCode,
            ],
        ];
    }
}
