<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\TrainingCalendarController;
use App\Http\Controllers\RouteController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\PostCommentController;
use App\Http\Controllers\ClubController;
use App\Http\Controllers\SegmentController;
use App\Http\Controllers\GoalController;
use App\Http\Controllers\ChallengeController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\FollowController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});

// Public profile
Route::get('/profile/{username}', [ProfileController::class, 'showByUsername']);

// Public feed
Route::get('/activities/feed', [ActivityController::class, 'feed']);

// Public routes explore
Route::get('/routes/explore', [RouteController::class, 'explore']);

// Public feed posts
Route::get('/feed', [PostController::class, 'index']);

// Public clubs
Route::get('/clubs', [ClubController::class, 'index']);
Route::get('/clubs/{club}', [ClubController::class, 'show']);
Route::get('/clubs/{club}/members', [ClubController::class, 'members']);

// Public segments
Route::get('/segments', [SegmentController::class, 'index']);
Route::get('/segments/{segment}', [SegmentController::class, 'show'])->where('segment', '[0-9]+');
Route::get('/segments/{segment}/leaderboard', [SegmentController::class, 'leaderboard'])->where('segment', '[0-9]+');

// Public challenges
Route::get('/challenges', [ChallengeController::class, 'index']);
Route::get('/challenges/{challenge}', [ChallengeController::class, 'show'])->where('challenge', '[0-9]+');
Route::get('/challenges/{challenge}/leaderboard', [ChallengeController::class, 'leaderboard'])->where('challenge', '[0-9]+');

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me', [AuthController::class, 'me']);

    // Profile
    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'show']);
        Route::put('/', [ProfileController::class, 'update']);
        Route::post('/avatar', [ProfileController::class, 'uploadAvatar']);
        Route::delete('/avatar', [ProfileController::class, 'deleteAvatar']);
    });

    // Activities
    Route::prefix('activities')->group(function () {
        Route::get('/', [ActivityController::class, 'index']);
        Route::post('/', [ActivityController::class, 'store']);
        Route::post('/upload', [ActivityController::class, 'upload']);
        Route::get('/following', [ActivityController::class, 'followingFeed']);
        Route::get('/{activity}', [ActivityController::class, 'show']);
        Route::put('/{activity}', [ActivityController::class, 'update']);
        Route::delete('/{activity}', [ActivityController::class, 'destroy']);
    });

    // Training Calendar
    Route::prefix('training')->group(function () {
        Route::get('/', [TrainingCalendarController::class, 'index']);
        Route::get('/summary', [TrainingCalendarController::class, 'summary']);
        Route::post('/', [TrainingCalendarController::class, 'store']);
        Route::get('/{trainingLog}', [TrainingCalendarController::class, 'show']);
        Route::put('/{trainingLog}', [TrainingCalendarController::class, 'update']);
        Route::delete('/{trainingLog}', [TrainingCalendarController::class, 'destroy']);
    });

    // Routes
    Route::prefix('routes')->group(function () {
        Route::get('/', [RouteController::class, 'index']);
        Route::post('/', [RouteController::class, 'store']);
        Route::get('/{userRoute}', [RouteController::class, 'show']);
        Route::put('/{userRoute}', [RouteController::class, 'update']);
        Route::delete('/{userRoute}', [RouteController::class, 'destroy']);
    });

    // Posts
    Route::prefix('posts')->group(function () {
        Route::get('/my', [PostController::class, 'myPosts']);
        Route::post('/', [PostController::class, 'store']);
        Route::get('/{post}', [PostController::class, 'show']);
        Route::delete('/{post}', [PostController::class, 'destroy']);
        Route::post('/{post}/like', [PostController::class, 'like']);
        Route::delete('/{post}/like', [PostController::class, 'unlike']);
        Route::get('/{post}/comments', [PostCommentController::class, 'index']);
        Route::post('/{post}/comments', [PostCommentController::class, 'store']);
        Route::delete('/{post}/comments/{postComment}', [PostCommentController::class, 'destroy']);
    });

    // Clubs
    Route::prefix('clubs')->group(function () {
        Route::get('/my', [ClubController::class, 'myClubs']);
        Route::post('/', [ClubController::class, 'store']);
        Route::put('/{club}', [ClubController::class, 'update']);
        Route::delete('/{club}', [ClubController::class, 'destroy']);
        Route::post('/{club}/join', [ClubController::class, 'join']);
        Route::delete('/{club}/leave', [ClubController::class, 'leave']);
        Route::post('/{club}/logo', [ClubController::class, 'uploadLogo']);
    });

    // Segments
    Route::prefix('segments')->group(function () {
        Route::get('/my', [SegmentController::class, 'mySegments']);
        Route::post('/', [SegmentController::class, 'store']);
        Route::put('/{segment}', [SegmentController::class, 'update']);
        Route::delete('/{segment}', [SegmentController::class, 'destroy']);
        Route::post('/{segment}/efforts', [SegmentController::class, 'logEffort']);
        Route::get('/{segment}/efforts/my', [SegmentController::class, 'myEfforts']);
    });

    // Follow / Unfollow
    Route::post('/users/{user}/follow', [FollowController::class, 'follow']);
    Route::delete('/users/{user}/follow', [FollowController::class, 'unfollow']);
    Route::get('/users/{user}/followers', [FollowController::class, 'followers']);
    Route::get('/users/{user}/following', [FollowController::class, 'following']);
    Route::get('/users/{user}/is-following', [FollowController::class, 'isFollowing']);
    Route::get('/users/{user}/activities', [FollowController::class, 'userActivities']);

    // Notifications
    Route::prefix('notifications')->group(function () {
        Route::get('/', [NotificationController::class, 'index']);
        Route::get('/unread-count', [NotificationController::class, 'unreadCount']);
        Route::post('/mark-all-read', [NotificationController::class, 'markAllRead']);
        Route::post('/{notification}/mark-read', [NotificationController::class, 'markRead']);
        Route::delete('/{notification}', [NotificationController::class, 'destroy']);
    });

    // Goals
    Route::prefix('goals')->group(function () {
        Route::get('/', [GoalController::class, 'index']);
        Route::post('/', [GoalController::class, 'store']);
        Route::get('/{goal}', [GoalController::class, 'show']);
        Route::put('/{goal}', [GoalController::class, 'update']);
        Route::delete('/{goal}', [GoalController::class, 'destroy']);
    });

    // Challenges
    Route::prefix('challenges')->group(function () {
        Route::get('/my', [ChallengeController::class, 'myChallenges']);
        Route::post('/', [ChallengeController::class, 'store']);
        Route::put('/{challenge}', [ChallengeController::class, 'update']);
        Route::delete('/{challenge}', [ChallengeController::class, 'destroy']);
        Route::post('/{challenge}/join', [ChallengeController::class, 'join']);
        Route::delete('/{challenge}/leave', [ChallengeController::class, 'leave']);
        Route::put('/{challenge}/progress', [ChallengeController::class, 'updateProgress']);
    });
});
