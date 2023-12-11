<?php

use App\Models\AchievementCommentType;
use App\Models\AchievementLessonType;
use App\Models\BadgeType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\CustomTestCase;

class AchievementsControllerTest extends CustomTestCase
{
    use RefreshDatabase;

    private array $headers;

    public function setUp(): void
    {
        parent::setUp();
        $this->headers = [
            'Authorization' => 'Bearer ' . $this->getLoginToken(),
            'Accept' => 'application/json'
        ];
    }

    public function testSuccessGettingEmptyAttachment()
    {
        $mergedNames = AchievementCommentType::pluck('name')->merge(AchievementLessonType::pluck('name'));
        $nextAvailableAchievements = $mergedNames->unique()->values()->all();

        $response = $this->get("/api/users/1/achievements", $this->headers);
        $jsonResponse = $response->json();

        $response->assertStatus(200);
        $this->assertEquals($nextAvailableAchievements, $jsonResponse['next_available_achievements']);
        $this->assertEquals([], $jsonResponse['unlocked_achievements']);

    }

    public function testSuccessWriteComment()
    {
        $userData = [
            'body' => 'test comment'
        ];
        $response = $this->post("/api/comments/create", data: $userData, headers: $this->headers);
        $response->assertStatus(201)->assertJson(["message" => "Comment added successfully"]);
    }


    public function testFailureWriteComment()
    {

        $response = $this->post("/api/comments/create", data: [], headers: $this->headers);
        $response->assertStatus(422)->assertJson([
            'message' => 'The body field is required.',
            'errors' => [
                'body' => [
                    0 => 'The body field is required.'
                ]
            ]
        ]);
    }

    public function watchLesson($id)
    {
        $response = $this->post("/api/lessons/" . $id . "/watch", headers: $this->headers);
        $response->assertStatus(200)->assertJson(["message" => "Lesson watched successfully"]);
    }

    public function testSuccessWatchLesson()
    {

        $response = $this->post("/api/lessons/1/watch", headers: $this->headers);
        $response->assertStatus(200)->assertJson(["message" => "Lesson watched successfully"]);
    }

    public function testNotFoundLesson()
    {

        $response = $this->post("/api/lessons/88/watch", headers: $this->headers);
        $response->assertStatus(500)->assertJson(['error' => 'Lesson is not found']);
    }

    public function testAlreadyWatched()
    {

        $response = $this->post("/api/lessons/1/watch", headers: $this->headers);
        $response->assertStatus(200)->assertJson(["message" => "Lesson watched successfully"]);
        $response = $this->post("/api/lessons/1/watch", headers: $this->headers);
        $response->assertStatus(200)->assertJson(["message" => "Lesson was already watched"]);
    }

    public function testSuccessGettingAttachment()
    {
        $mergedNames = AchievementCommentType::pluck('name')->merge(AchievementLessonType::pluck('name'));
        $nextAvailableAchievements = $mergedNames->unique()->values()->all();

        $response = $this->get("/api/users/1/achievements", $this->headers);
        $jsonResponse = $response->json();

        $response->assertStatus(200);
        $this->assertEquals($nextAvailableAchievements, $jsonResponse['next_available_achievements']);
        $this->assertEquals([], $jsonResponse['unlocked_achievements']);

    }

    public function testSuccessWithFirstAchievementsWithComments()
    {

        $mergedNames = AchievementCommentType::pluck('name')->merge(AchievementLessonType::pluck('name'));
        $nextAvailableAchievements = $mergedNames->unique()->values()->all();

        $firstAchievement = AchievementCommentType::orderBy('condition')->first();
        for ($i = 0; $i < $firstAchievement->condition; $i++) {
            $this->testSuccessWriteComment();
        }

        if (($key = array_search($firstAchievement->name, $nextAvailableAchievements)) !== false) {
            unset($nextAvailableAchievements[$key]);
        }
        $response = $this->get("/api/users/1/achievements", $this->headers);
        $jsonResponse = $response->json();
        $response->assertStatus(200);
        $this->assertSameSize($nextAvailableAchievements, $jsonResponse['next_available_achievements']);
        $this->assertEquals([$firstAchievement->name], $jsonResponse['unlocked_achievements']);
    }
    public function testSuccessWithFirstAchievementsWithLessons()
    {

        $mergedNames = AchievementCommentType::pluck('name')->merge(AchievementLessonType::pluck('name'));
        $nextAvailableAchievements = $mergedNames->unique()->values()->all();

        $firstAchievement = AchievementLessonType::orderBy('condition')->first();
        for ($i = 1; $i <= $firstAchievement->condition; $i++) {
            $this->watchLesson($i);
        }

        if (($key = array_search($firstAchievement->name, $nextAvailableAchievements)) !== false) {
            unset($nextAvailableAchievements[$key]);
        }
        $response = $this->get("/api/users/1/achievements", $this->headers);
        $jsonResponse = $response->json();
        $response->assertStatus(200);
        $this->assertSameSize($nextAvailableAchievements, $jsonResponse['next_available_achievements']);
        $this->assertEquals([$firstAchievement->name], $jsonResponse['unlocked_achievements']);
    }
    public function testSuccessWithSecondBadge()
    {

        $mergedConditions = AchievementCommentType::pluck('condition')->merge(AchievementLessonType::pluck('condition'));
        $achievements = $mergedConditions->values()->all();

        $secondBadgeType = BadgeType::orderBy('condition')->skip(1)->take(1)->first();

        $requiredAchievements = 0;
        for ($i = 0; $i < $secondBadgeType->condition; $i++) {
            $requiredAchievements+=$achievements[$i];
        }
        for ($i = 0; $i < $requiredAchievements; $i++) {
            $this->testSuccessWriteComment();
        }
        $thirdBadgeType = BadgeType::orderBy('condition')->skip(2)->take(1)->first();

        $response = $this->get("/api/users/1/achievements", $this->headers);
        $jsonResponse = $response->json();
        $response->assertStatus(200);
        $this->assertEquals($secondBadgeType->name, $jsonResponse['current_badge']);
        $this->assertEquals($thirdBadgeType->name, $jsonResponse['next_badge']);
        $this->assertCount($secondBadgeType->condition, $jsonResponse['unlocked_achievements']);
        $this->assertCount(count($achievements) - $secondBadgeType->condition, $jsonResponse['next_available_achievements']);
    }
}
