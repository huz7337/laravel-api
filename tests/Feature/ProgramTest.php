<?php

namespace Tests\Feature;

use App\Models\Program;
use App\Models\User;
use App\Models\Workout;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class ProgramTest extends TestCase
{

    public function test_create_program()
    {
        $data = Program::factory()->make()->toArray();
        Storage::fake('avatars');
        $file = UploadedFile::fake()->image('avatar.jpg');
        $data['cover'] = $file;

        $this->post('/api/programs', $data)
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) =>
            $json->whereAllType([
                'id' => 'integer',
                'name' => 'string',
                'description' => 'string',
                'cover' => 'string'
            ])->etc())
            ->assertJsonPath('name', $data['name']);

        $item = Program::where('name', $data['name'])->first();
        if ($item->cover) {
            Storage::disk('s3')->delete($item->photo);
        }
    }

    /**
     * @throws \Throwable
     */
    public function test_list_programs()
    {
        Program::factory()->count(10)->create();

        // test listing
        $this->get('/api/programs')
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) =>
            $json->whereAllType([
                'currentPage' => 'integer',
                'perPage' => 'integer',
                'lastPage' => 'integer',
                'total' => 'integer',
                'items' => 'array',
                'items.0.id' => 'integer',
                'items.0.name' => 'string',
                'items.0.description' => 'string|null',
                'items.0.cover' => 'string|null',
                'items.0.duration' => 'integer',
                'items.0.number_of_athletes' => 'integer',
            ])->etc())
            ->assertJsonPath('currentPage', 1)
            ->assertJsonPath('perPage', 30);

        // test search
        $item = Program::first();
        $params = ['search' => $item->name];
        $response = $this->call('GET', '/api/programs', $params);
        $response->assertOk()
            ->assertJsonPath('items.0.name', $item->name);

        // test sort by ID
        $params = [
            'sort_column' =>'id',
            'sort_direction' => 'desc'
        ];
        $response = $this->call('GET', '/api/programs', $params);
        $response->assertOk();
        $result = $response->decodeResponseJson();
        $this->assertGreaterThan($result['items'][1]['id'], $result['items'][0]['id']);

        // test sort by name
        $params = [
            'sort_column' =>'name',
            'sort_direction' => 'asc'
        ];
        $response = $this->call('GET', '/api/programs', $params);
        $response->assertOk();
        $result = $response->decodeResponseJson();
        $this->assertGreaterThanOrEqual(0, strcasecmp($result['items'][1]['name'], $result['items'][0]['name']));

        Program::factory()->count(5)->live()->create();

        // test sort by status
        $params = [
            'sort_column' =>'status',
            'sort_direction' => 'asc'
        ];
        $response = $this->call('GET', '/api/programs', $params);
        $response->assertOk()
            ->assertJsonPath('items.0.status', 'Live');

        // test filter by status
        $params = ['status' => 'live'];
        $response = $this->call('GET', '/api/programs', $params);
        $response->assertOk()
            ->assertJsonPath('total', 5);

    }


    public function test_show_program()
    {
        $item = Program::factory()->create();

        $this->get("/api/programs/{$item->id}")
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) =>
            $json->whereAllType($this->_jsonStructure())->etc())
            ->assertJsonPath('name', $item->name);
    }


    public function test_update_program()
    {
        $item = Program::factory()->create();

        $this->post("/api/programs/{$item->id}", ['name' => 'Edited program'])
            ->assertOk()
            ->assertJsonPath('id', $item->id)
            ->assertJsonPath('name', 'Edited program');
    }


    public function test_delete_program()
    {
        $item = Program::factory()->create();

        $this->delete("/api/programs/{$item->id}")
            ->assertOk()
            ->assertJsonPath('message', __('The program has been removed.'));
    }


    public function test_duplicate_program()
    {
        // create a 21-day program
        $program = $this->_create21DayProgram();

        $this->put("/api/programs/{$program->id}/duplicate")
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) =>
            $json->whereAllType($this->_jsonStructure())->etc())
            ->assertJsonPath('name', $program->name . ' Copy');

        /**
         * @var Program $newProgram
         */
        $newProgram = Program::where('name', $program->name . ' Copy')->first();
        $this->assertEquals(21, $newProgram->workouts()->count());

    }


    public function test_bulk_assign_program()
    {
        // create a 21-day program
        $program = $this->_create21DayProgram();
        // get 10 athletes
        $athletes = User::role(User::ROLE_ATHLETE)->limit(10)->pluck('id')->toArray();

        $data = [
            'user_ids' => $athletes,
            'start_date' => Carbon::now()->next('Monday'),
            'start_day' => 1,
        ];

        $this->put("/api/programs/{$program->id}/assign", $data)
            ->assertOk()
            ->assertJsonPath('message', __('Great! You have successfully assigned all the selected athletes to the program.'));

    }


    /**
     * The json structure of a program
     * @return string[]
     */
    private function _jsonStructure(): array
    {
        return [
            'id' => 'integer',
            'name' => 'string',
            'description' => 'string|null',
            'cover' => 'string|null',
            'duration' => 'integer',
            'number_of_athletes' => 'integer',
        ];
    }


    /**
     * Create a 21-day program
     * @return Program
     */
    private function _create21DayProgram(): Program
    {
        // create a 21-day program
        $program = Program::factory()->create();

        for ($i = 0; $i < 3; $i++) {
            $workouts = Workout::factory()->count(6)->active()->make()->toArray();
            $workouts[] = Workout::factory()->recovery()->make()->toArray();

            foreach ($workouts as $workout) {
                $this->post("/api/programs/{$program->id}/workouts", $workout)
                    ->assertOk();
            }
        }

        return $program;
    }

}
