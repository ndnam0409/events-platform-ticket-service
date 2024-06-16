<?php

namespace Database\Factories;

use App\Models\Ticket;
use Illuminate\Database\Eloquent\Factories\Factory;

class TicketFactory extends Factory
{
    protected $model = Ticket::class;

    public function definition()
    {
        return [
            'location' => $this->faker->address,
            'area' => $this->faker->randomElement(['VIP', 'Regular', 'Economy']),
            'seat' => $this->faker->bothify('??##'),
            'price' => $this->faker->randomFloat(2, 10, 200),
            'isSold' => $this->faker->boolean,
            'eventID' => $this->faker->numberBetween(1, 50), 
        ];
    }
}
