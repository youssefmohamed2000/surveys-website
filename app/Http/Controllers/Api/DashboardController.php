<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SurveyAnswerResource;
use App\Http\Resources\SurveyDashboardResource;
use App\Models\Survey;
use App\Models\SurveyAnswer;

class DashboardController extends Controller
{
  public function index()
  {

    $user = auth()->user();

    // total number of surveys
    $numberOfSurveys = Survey::query()
      ->where('user_id', $user->id)
      ->count();

    // latest surveys
    $latestSurvey = Survey::query()
      ->where('user_id', $user->id)
      ->latest('created_at')
      ->first();

    // total number of answers
    $numberOfAnswers = SurveyAnswer::query()
      ->join('surveys', 'survey_answers.survey_id', '=', 'surveys.id')
      ->where('surveys.user_id', $user->id)
      ->count();

    // latest 5 answers
    $latestAnswers = SurveyAnswer::query()
      ->join('surveys', 'survey_answers.survey_id', '=', 'surveys.id')
      ->where('surveys.user_id', $user->id)
      ->latest('end_date')
      ->limit(5)
      ->getModels('survey_answers.*');

    $data = [
      'numberOfSurveys' => $numberOfSurveys,
      'latestSurvey' => $latestSurvey ? new SurveyDashboardResource($latestSurvey) : null,
      'numberOfAnswers' => $numberOfAnswers,
      'latestAnswers' => SurveyAnswerResource::collection($latestAnswers),
    ];

    return $this->sendResponse($data, 'dashboard information sent successfully', 200);
  } // end of index
} // end of class
