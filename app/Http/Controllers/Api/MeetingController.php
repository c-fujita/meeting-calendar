<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MeetingController extends Controller
{
    public function index()
    {
        try {
            $response = Http::withHeaders([
                'User-Agent' => config('services.meeting_api.user_agent'),
            ])->get(config('services.meeting_api.url'));

            $data = $response->json();
            // ログに記載
            Log::info('Meeting API response', $data);

            $meetings = [];
            foreach ($data['meetings'] as $date => $dailyMeetings) {
                foreach ($dailyMeetings as $meeting) {
                    $meetings[] = [
                        'date' => $date,
                        'summary' => $meeting['summary'],
                        'start' => $meeting['start'],
                        'end' => $meeting['end'],
                        'timezone' => $meeting['timezone'],
                    ];
                }
            }
            return response()->json([
                'working_hours' => $data['working_hours'],
                'meetings' => $meetings,
            ]);
        } catch (\Exception $e) {

            Log::error(
                'データ取得失敗',
                [
                    'message' => $e->getMessage()
                ]
            );

            return response()->json([
                'message' =>
                    'データの取得に失敗しました。'
            ], 500);
        }
    }
}
