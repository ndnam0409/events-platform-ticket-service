<?php

namespace Database\Factories;

use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;

class EventFactory extends Factory
{
    protected $model = Event::class;

    public function definition()
    {
        return [
            'eventName' => $this->faker->sentence,
            'startDate' => $this->faker->dateTimeBetween('+1 week', '+1 month'),
            'endDate' => $this->faker->dateTimeBetween('+1 month', '+2 months'),
            'location' => $this->faker->address,
            'capacity' => $this->faker->numberBetween(50, 500),
        ];
    }
}
