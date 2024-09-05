<?php

namespace Tests\Feature;

use App\Models\CoworkingOpeningHours;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class CoworkingOpeningHoursTest extends TestCase
{

  use RefreshDatabase;

  public function setUp(): void
  {
    parent::setUp();
  }

  public function test_coworking_opening_index()
  {
    $coworking = $this->loginWithPartner();

    $payload = [
      'settings' => [
        [
          'day_of_week' => 'monday',
          'opening' => '08:00',
          'closing' => '18:00',
        ],
        [
          'day_of_week' => 'tuesday',
          'opening' => '08:00',
          'closing' => '18:00',
        ],
        [
          'day_of_week' => 'wednesday',
          'opening' => '08:00',
          'closing' => '18:00',
        ],
        [
          'day_of_week' => 'thursday',
          'opening' => '08:00',
          'closing' => '18:00',
        ],
        [
          'day_of_week' => 'friday',
          'opening' => '08:00',
          'closing' => '18:00',
        ],
        [
          'day_of_week' => 'saturday',
          'opening' => '08:00',
          'closing' => '18:00',
        ],
        [
          'day_of_week' => 'sunday',
          'opening' => '08:00',
          'closing' => '18:00',
        ]
      ]
    ];

    $params = [
      'user_uuid' => Auth::user()->uuid,
      'coworking_uuid' => $coworking->uuid
    ];

    $this->post(route('coworking.opening-hour.store', $params), $payload);

    $response = $this->get(route('coworking.opening-hour.index', $params));
    $response->assertStatus(200);
    $response->assertJsonStructure([
      'data' => [
        '*' => [
          'uuid',
          'day_of_week',
          'opening',
          'closing',
        ]
      ]
    ]);
    $response->assertJsonCount(7, 'data');
  }

  public function test_coworking_opening_store()
  {
    $coworking = $this->loginWithPartner();

    $payload = [
      'settings' => [
        [
          'day_of_week' => 'monday',
          'opening' => '08:00',
          'closing' => '18:00',
        ],
        [
          'day_of_week' => 'tuesday',
          'opening' => '08:00',
          'closing' => '18:00',
        ],
        [
          'day_of_week' => 'wednesday',
          'opening' => '08:00',
          'closing' => '18:00',
        ],
        [
          'day_of_week' => 'thursday',
          'opening' => '08:00',
          'closing' => '18:00',
        ],
        [
          'day_of_week' => 'friday',
          'opening' => '08:00',
          'closing' => '18:00',
        ],
        [
          'day_of_week' => 'saturday',
          'opening' => '08:00',
          'closing' => '18:00',
        ],
        [
          'day_of_week' => 'sunday',
          'opening' => '08:00',
          'closing' => '18:00',
        ]
      ]
    ];

    $params = [
      'user_uuid' => Auth::user()->uuid,
      'coworking_uuid' => $coworking->uuid
    ];
    $response = $this->post(route('coworking.opening-hour.store', $params), $payload);
    $response->assertStatus(200);
  }

  public function test_coworking_opening_store_with_invalid_opening_time()
  {
    $coworking = $this->loginWithPartner();

    $payload = [
      'settings' => [
        [
          'day_of_week' => 'monday',
          'opening' => '08:00',
          'closing' => '18:00',
        ],
        [
          'day_of_week' => 'tuesday',
          'opening' => '08:00',
          'closing' => '07:00',
        ],
        [
          'day_of_week' => 'wednesday',
          'opening' => '08:00',
          'closing' => '18:00',
        ],
        [
          'day_of_week' => 'thursday',
          'opening' => '08:00',
          'closing' => '18:00',
        ],
        [
          'day_of_week' => 'friday',
          'opening' => '08:00',
          'closing' => '18:00',
        ],
        [
          'day_of_week' => 'saturday',
          'opening' => '08:00',
          'closing' => '18:00',
        ],
        [
          'day_of_week' => 'sunday',
          'opening' => '08:00',
          'closing' => '17:00',
        ]
      ]
    ];

    $params = [
      'user_uuid' => Auth::user()->uuid,
      'coworking_uuid' => $coworking->uuid
    ];
    $response = $this->post(route('coworking.opening-hour.store', $params), $payload);
    $response->assertStatus(400);
  }

  public function test_destroy_a_opening_hours()
  {
    $coworking = $this->loginWithPartner();

    $payload = [
      'settings' => [
        [
          'day_of_week' => 'monday',
          'opening' => '08:00',
          'closing' => '18:00',
        ],
        [
          'day_of_week' => 'tuesday',
          'opening' => '08:00',
          'closing' => '18:00',
        ],
        [
          'day_of_week' => 'wednesday',
          'opening' => '08:00',
          'closing' => '18:00',
        ],
        [
          'day_of_week' => 'thursday',
          'opening' => '08:00',
          'closing' => '18:00',
        ],
        [
          'day_of_week' => 'friday',
          'opening' => '08:00',
          'closing' => '18:00',
        ],
        [
          'day_of_week' => 'saturday',
          'opening' => '08:00',
          'closing' => '18:00',
        ],
        [
          'day_of_week' => 'sunday',
          'opening' => '08:00',
          'closing' => '18:00',
        ]
      ]
    ];

    $params = [
      'user_uuid' => Auth::user()->uuid,
      'coworking_uuid' => $coworking->uuid
    ];

    $response = $this->post(route('coworking.opening-hour.store', $params), $payload);

    $openingUuid = $response->json('data.2.uuid');

    $responseDelete = $this->delete(route('coworking.opening-hour.destroy' , [...$params, 'uuid' => $openingUuid]));

    $responseDelete->assertStatus(200);
  }

  public function test_destroy_bulks_opening_hours()
  {

    $coworking = $this->loginWithPartner();

    $payload = [
      'settings' => [
        [
          'day_of_week' => 'monday',
          'opening' => '08:00',
          'closing' => '18:00',
        ],
        [
          'day_of_week' => 'tuesday',
          'opening' => '08:00',
          'closing' => '18:00',
        ]
      ]
    ];

    $params = [
      'user_uuid' => Auth::user()->uuid,
      'coworking_uuid' => $coworking->uuid
    ];
    $response = $this->post(route('coworking.opening-hour.store', $params), $payload);

    $uuids['uuids'][] = $response->json('data.0.uuid');
    $uuids['uuids'][] = $response->json('data.1.uuid');

    $responseDelete = $this->post(route('coworking.opening-hour.destroy.bulk', $params), $uuids);
    $responseDelete->assertStatus(200);
    $this->assertEmpty(CoworkingOpeningHours::where('coworking_id', $coworking->id)->whereIn('uuid', $uuids['uuids'])->get(), 'Opening hours not deleted');
  }

  public function test_factory()
  {
    $coworking = $this->loginWithPartner();
    $opening = CoworkingOpeningHours::factory()->create([
      'coworking_id' => $coworking->id
    ]);

    $this->assertNotEmpty($opening);
    $this->assertTrue($opening->coworking_id === $coworking->id);
  }
}
