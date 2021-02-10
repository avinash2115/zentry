<?php

use App\Components\Sessions\Session\SessionReadonlyContract;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class RecordedMigration
 */
class RecordedMigration extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(
            'sessions',
            static function (Blueprint $table) {
                $table->smallInteger('status')->after('type')->default(SessionReadonlyContract::STATUS_NEW);
            }
        );

        Schema::table(
            'sessions',
            static function (Blueprint $table) {
                $table->text('thumbnail')->nullable()->after('tags');
            }
        );

        Schema::table(
            'sessions_streams',
            static function (Blueprint $table) {
                $table->tinyInteger('convert_progress')->default(0)->after('url');
            }
        );

        Schema::table(
            'sessions_pois',
            static function (Blueprint $table) {
                $table->text('thumbnail')->nullable()->after('tags');
                $table->text('stream')->nullable()->after('thumbnail');
            }
        );

        Schema::table(
            'sessions_recorded',
            static function (Blueprint $table) {
                $table->smallInteger('status')->after('type')->default(SessionReadonlyContract::STATUS_WRAPPED);
                $table->dropForeign('sessions_recorded_user_id_foreign');
            }
        );

        DB::table('sessions')->get()->each(
            static function (object $item) {
                switch (true) {
                    case $item->ended_at !== null:
                        $status = SessionReadonlyContract::STATUS_ENDED;
                    break;
                    case $item->started_at !== null && $item->ended_at === null:
                        $status = SessionReadonlyContract::STATUS_STARTED;
                    break;
                    default:
                        $status = SessionReadonlyContract::STATUS_NEW;
                    break;
                }

                DB::table('sessions')->where('id', '=', $item->id)->update(
                    [
                        'status' => $status,
                    ]
                );
            }
        );

        Schema::table(
            'sessions_recorded_pois_participants',
            static function (Blueprint $table) {
                $table->dropForeign('sessions_recorded_pois_participants_participant_id_foreign');
                $table->dropForeign('sessions_recorded_pois_participants_poi_id_foreign');
            }
        );
        Schema::table(
            'sessions_recorded_participants',
            static function (Blueprint $table) {
                $table->dropForeign('sessions_recorded_participants_participant_id_foreign');
                $table->dropForeign('sessions_recorded_participants_recorded_session_id_foreign');
            }
        );
        Schema::table(
            'sessions_recorded_pois',
            static function (Blueprint $table) {
                $table->dropForeign('sessions_recorded_pois_recorded_session_id_foreign');
            }
        );
        Schema::table(
            'sessions_recorded_streams',
            static function (Blueprint $table) {
                $table->dropForeign('sessions_recorded_streams_recorded_session_id_foreign');
            }
        );

        DB::insert(
            "INSERT INTO sessions (id, user_id, name, status, type, description, geo, tags, thumbnail, started_at, ended_at, created_at, updated_at)
            SELECT id, user_id, name, status, type, description, geo, tags, thumbnail, started_at, ended_at, created_at, updated_at
            FROM sessions_recorded"
        );

        DB::insert(
            "INSERT INTO sessions_participants (session_id, participant_id)
            SELECT recorded_session_id, participant_id
            FROM sessions_recorded_participants"
        );

        DB::insert(
            "INSERT INTO sessions_streams (id, session_id, type, name, url, convert_progress, created_at)
            SELECT id, recorded_session_id, type, name, url, convert_progress, created_at
            FROM sessions_recorded_streams"
        );

        DB::insert(
            "INSERT INTO sessions_pois (id, session_id, type, thumbnail, stream, tags, started_at, ended_at, created_at)
            SELECT id, recorded_session_id, type, thumbnail, stream, tags, started_at, ended_at, created_at
            FROM sessions_recorded_pois"
        );

        DB::insert(
            "INSERT INTO sessions_pois_participants (id, poi_id, participant_id, started_at, ended_at)
            SELECT id, poi_id, participant_id, started_at, ended_at
            FROM sessions_recorded_pois_participants"
        );

        DB::table('sessions_recorded_pois')->truncate();
        DB::table('sessions_recorded_pois_participants')->truncate();
        DB::table('sessions_recorded_streams')->truncate();
        DB::table('sessions_recorded_participants')->truncate();
        DB::table('sessions_recorded')->truncate();

        Schema::drop('sessions_recorded_pois_participants');
        Schema::drop('sessions_recorded_pois');
        Schema::drop('sessions_recorded_streams');
        Schema::drop('sessions_recorded_participants');
        Schema::drop('sessions_recorded');

        try {
            $this->processTranscript();

            Schema::connection('mongodb')->drop('sessions_recorded_transcriptions');
        } catch (Exception $exception) {
            report($exception);
        }
    }

    /**
     * @param int $limit
     * @param int $offset
     */
    private function processTranscript(int $limit = 1000, int $offset = 0): void
    {
        if (DB::connection('mongodb')
            ->collection('sessions_recorded_transcriptions')
            ->limit($limit)
            ->offset($offset)
            ->get()
            ->each(
                function (array $item) {
                    $this->insert($item);
                }
            )
            ->isNotEmpty()) {
            $this->processTranscript($limit, $limit + $offset);
        }
    }

    /**
     * @param array $item
     */
    private function insert(array $item): void
    {
        DB::connection('mongodb')->collection('sessions_transcriptions')->insert(
            [
                '_id' => Arr::get($item, '_id'),
                'userIdentity' => Arr::get($item, 'userIdentity'),
                'sessionIdentity' => Arr::get($item, 'recordedIdentity'),
                'poiIdentity' => Arr::get($item, 'poiIdentity'),
                'word' => Arr::get($item, 'word'),
                'startedAt' => Arr::get($item, 'startedAt'),
                'endedAt' => Arr::get($item, 'endedAt'),
                'speakerTag' => Arr::get($item, 'speakerTag'),
                'createdAt' => Arr::get($item, 'createdAt'),
            ]
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}
