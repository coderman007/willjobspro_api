<?php

namespace App\Services;

use App\Models\Country;
use App\Models\State;
use App\Models\City;
use App\Models\ZipCode;

class LocationService
{
    public function createAndAssociateLocation($locationData, $user): bool|array
    {
        $country = Country::firstOrCreate(['iso_alpha_2' => $locationData['iso_alpha_2']], [
            'name' => $locationData['country'],
            'dial_code' => $locationData['dial_code']
        ]);

        $state = State::firstOrCreate(['name' => $locationData['state'], 'country_id' => $country->id]);
        $city = City::firstOrCreate(['name' => $locationData['city'], 'state_id' => $state->id]);
        $zipCode = ZipCode::firstOrCreate(['code' => $locationData['zip_code'], 'city_id' => $city->id]);

        // Validar la relación de ubicación
        $errors = [];

        // Validar estado
        if ($state->country_id !== $country->id) {
            $errors['state'] = ['The provided state does not belong to the specified country.'];
        }

        // Validar ciudad
        if ($city->state_id !== $state->id) {
            $errors['city'] = ['The provided city does not belong to the specified state.'];
        }

        // Validar código postal
        if ($zipCode->city_id !== $city->id) {
            $errors['zip_code'] = ['The provided zip code does not belong to the specified city.'];
        }

        if (!empty($errors)) {
            return ['errors' => $errors];
        }

        // Asociar las ubicaciones al usuario
        $user->country_id = $country->id;
        $user->state_id = $state->id;
        $user->city_id = $city->id;
        $user->zip_code_id = $zipCode->id;

        $user->save();

        return true;
    }
    public function updateAndAssociateLocation($locationData, $user): bool|array
    {
        $country = Country::firstOrCreate(['iso_alpha_2' => $locationData['iso_alpha_2']], [
            'name' => $locationData['country'],
            'dial_code' => $locationData['dial_code']
        ]);

        $state = State::firstOrCreate(['name' => $locationData['state'], 'country_id' => $country->id]);
        $city = City::firstOrCreate(['name' => $locationData['city'], 'state_id' => $state->id]);
        $zipCode = ZipCode::firstOrCreate(['code' => $locationData['zip_code'], 'city_id' => $city->id]);

        // Validar la relación de ubicación
        $errors = [];

        // Validar estado
        if ($state->country_id !== $country->id) {
            $errors['state'] = ['The provided state does not belong to the specified country.'];
        }

        // Validar ciudad
        if ($city->state_id !== $state->id) {
            $errors['city'] = ['The provided city does not belong to the specified state.'];
        }

        // Validar código postal
        if ($zipCode->city_id !== $city->id) {
            $errors['zip_code'] = ['The provided zip code does not belong to the specified city.'];
        }

        if (!empty($errors)) {
            return ['errors' => $errors];
        }

        // Asociar las ubicaciones al usuario
        $user->country_id = $country->id;
        $user->state_id = $state->id;
        $user->city_id = $city->id;
        $user->zip_code_id = $zipCode->id;

        $user->save();

        return true;
    }
    public function createAndAssociateLocationForJob($locationData, $job): bool|array
    {
        $country = Country::firstOrCreate(['iso_alpha_2' => $locationData['iso_alpha_2']], [
            'name' => $locationData['country'],
            'dial_code' => $locationData['dial_code']
        ]);

        $state = State::firstOrCreate(['name' => $locationData['state'], 'country_id' => $country->id]);
        $city = City::firstOrCreate(['name' => $locationData['city'], 'state_id' => $state->id]);
        $zipCode = ZipCode::firstOrCreate(['code' => $locationData['zip_code'], 'city_id' => $city->id]);

        // Validar la relación de ubicación
        $errors = [];

        // Validar estado
        if ($state->country_id !== $country->id) {
            $errors['state'] = ['The provided state does not belong to the specified country.'];
        }

        // Validar ciudad
        if ($city->state_id !== $state->id) {
            $errors['city'] = ['The provided city does not belong to the specified state.'];
        }

        // Validar código postal
        if ($zipCode->city_id !== $city->id) {
            $errors['zip_code'] = ['The provided zip code does not belong to the specified city.'];
        }

        if (!empty($errors)) {
            return ['errors' => $errors];
        }

        // Asociar las ubicaciones al trabajo
        $job->country_id = $country->id;
        $job->state_id = $state->id;
        $job->city_id = $city->id;
        $job->zip_code_id = $zipCode->id;

        $job->save();

        return true;
    }
    public function updateAndAssociateLocationForJob($locationData, $job): bool|array
    {
        $country = Country::firstOrCreate(['iso_alpha_2' => $locationData['iso_alpha_2']], [
            'name' => $locationData['country'],
            'dial_code' => $locationData['dial_code']
        ]);

        $state = State::firstOrCreate(['name' => $locationData['state'], 'country_id' => $country->id]);
        $city = City::firstOrCreate(['name' => $locationData['city'], 'state_id' => $state->id]);
        $zipCode = ZipCode::firstOrCreate(['code' => $locationData['zip_code'], 'city_id' => $city->id]);

        // Validar la relación de ubicación
        $errors = [];

        // Validar estado
        if ($state->country_id !== $country->id) {
            $errors['state'] = ['The provided state does not belong to the specified country.'];
        }

        // Validar ciudad
        if ($city->state_id !== $state->id) {
            $errors['city'] = ['The provided city does not belong to the specified state.'];
        }

        // Validar código postal
        if ($zipCode->city_id !== $city->id) {
            $errors['zip_code'] = ['The provided zip code does not belong to the specified city.'];
        }

        if (!empty($errors)) {
            return ['errors' => $errors];
        }

        // Asociar las ubicaciones a la oferta de trabajo
        $job->country_id = $country->id;
        $job->state_id = $state->id;
        $job->city_id = $city->id;
        $job->zip_code_id = $zipCode->id;

        $job->save();

        return true;
    }

}
