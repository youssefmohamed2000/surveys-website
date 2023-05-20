<?php

namespace App\Http\Controllers\api;

use App\Enums\QuestionType;
use App\Models\Survey;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSurveyAnswerRequest;
use App\Http\Resources\SurveyResource;
use App\Http\Requests\StoreSurveyRequest;
use App\Http\Requests\UpdateSurveyRequest;
use App\Models\SurveyAnswer;
use App\Models\SurveyQuestion;
use App\Models\SurveyQuestionAnswer;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;


class SurveyController extends Controller
{
  public function index()
  {
    $surveys = Survey::query()->where('user_id', auth()->user()->id)->paginate(6);

    return $this->sendResponse(SurveyResource::collection($surveys), 'surveys sent successfully');
  } // end of index

  public function store(StoreSurveyRequest $request)
  {

    $validated = $request->validated();

    if (isset($validated['image'])) {
      $relativePath = $this->saveImage($validated['image']);
      $validated['image'] = $relativePath;
    }

    DB::beginTransaction();
    try {
      $survey = Survey::query()->create($validated);

      // storing questions 
      foreach ($validated['questions'] as  $question) {
        $question['survey_id'] = $survey->id;

        $this->createQuestion($question);
      }

      DB::commit();
    } catch (\Throwable $th) {
      DB::rollBack();
      throw $th;
    }

    return $this->sendResponse(new SurveyResource($survey), 'survey created successfully', 201);
  } // end of store

  public function show(Survey $survey)
  {
    if ($survey->user_id !== auth()->user()->id) {
      return abort(403, 'Unauthorized Action');
    }

    return $this->sendResponse(new SurveyResource($survey), 'survey sent successfully');
  } // end of show

  public function showForGuest(Survey $survey)
  {
    return $this->sendResponse(new SurveyResource($survey), 'survey sent successfully');
  } // end of showForGuest

  public function storeAnswer(StoreSurveyAnswerRequest $request, Survey $survey,)
  {
    $validated = $request->validated();

    try {
      DB::beginTransaction();

      $surveyAnswer = SurveyAnswer::query()->create([
        'survey_id' => $survey->id,
        'start_date' => date('Y-m-d H:i:s'),
        'end_date' => date('Y-m-d H:i:s'),
      ]);

      foreach ($validated['answers'] as $questionId => $answer) {
        $question = SurveyQuestion::query()
          ->where(['id' => $questionId, 'survey_id' => $survey->id])
          ->get();

        if (!$question) {
          return $this->sendError("Invalid Question ID: {$questionId}", [], 400);
        }

        $data = [
          'survey_question_id' => $questionId,
          'survey_answer_id' => $surveyAnswer->id,
          'answer' => is_array($answer) ? json_encode($answer) : $answer,
        ];

        SurveyQuestionAnswer::query()->create($data);
        DB::commit();
      }
    } catch (\Throwable $th) {
      DB::rollBack();
      throw $th;
    }

    return $this->sendResponse(null, 'answers saved successfully', 201);
  } // end of storeAnswer

  public function update(UpdateSurveyRequest $request, Survey $survey)
  {
    $validated = $request->validated();

    // check if request has image
    if (isset($validated['image'])) {
      $relativePath = $this->saveImage($validated['image']);
      $validated['image'] = $relativePath;
    }

    // check if was old image 
    if ($survey->image) {
      File::delete(public_path($survey->image));
    }

    $survey->update($validated);

    // get ids of existing questions
    $existingIds = $survey->questions()->pluck('id')->toArray();
    // get ids of new questions
    $newIds = Arr::pluck($validated['questions'], 'id');
    // find questions to delete
    $toDelete = array_diff($existingIds, $newIds);
    // finding questions to add
    $toAdd = array_diff($newIds, $existingIds);

    // delete questions
    SurveyQuestion::destroy($toDelete);

    // create new questions
    foreach ($validated['questions'] as  $question) {
      if (in_array($question['id'], $toAdd)) {
        $question['survey_id'] = $survey->id;
        $this->createQuestion($question);
      }
    }

    // update existing questions
    $questionMap = collect($validated['questions'])->keyBy('id');
    foreach ($survey->questions as $question) {
      if (isset($questionMap[$question->id])) {
        $this->updateQuestion($question, $questionMap[$question->id]);
      }
    }

    return $this->sendResponse(new SurveyResource($survey), 'survey updated successfully');
  } // end of update

  public function destroy(Survey $survey)
  {
    if ($survey->user_id !== auth()->user()->id) {
      return abort(403, 'Unauthorized Action');
    }

    $survey->delete();

    // check if was has image 
    if ($survey->image) {
      File::delete(public_path($survey->image));
    }
    return $this->sendResponse(new SurveyResource($survey), 'survey deleted successfully', 204);
  } // end of delete

  private function saveImage($image)
  {
    if (preg_match('/^data:image\/(\w+);base64,/', $image, $type)) {
      $image = substr($image, strpos($image, ',') + 1);

      // get file extension
      $type = strtolower($type[1]);

      // check if type is valid
      if (!in_array($type, ['png', 'jpg', 'jpeg', 'gif'])) {
        $this->sendError('image type is not valid');
      }

      $image = str_replace(' ', '+', $image);
      $image = base64_decode($image);

      if ($image === false) {
        $this->sendError('image decode failed');
      }
    } else {
      $this->sendError('image url is not valid');
    }

    $dir = 'images/';
    $file = Str::random() . '.' . $type;
    $absolutePath = public_path($dir);
    $relativePath = $dir . $file;

    if (!File::exists($absolutePath)) {
      File::makeDirectory($absolutePath, 0755, true);
    }

    file_put_contents($relativePath, $image);

    return $relativePath;
  } // end of saveImage

  private function createQuestion($question)
  {
    if (is_array($question['data'])) {
      // this because we can't save array in db so we save it as json
      $question['data'] = json_encode($question['data']);
    }

    $validator = Validator::make($question, [
      'question' => ['required', 'numeric', 'max:255'],
      'type' => ['required', new Enum(QuestionType::class)],
      'description' => ['nullable', 'string'],
      'data' => 'present',
      'survey_id' => [Rule::exists('surveys', 'id')],
    ]);

    return SurveyQuestion::query()->create($validator->validated());
  } // end of createQuestion

  private function updateQuestion(SurveyQuestion $question, $data)
  {
    if (is_array($data['data'])) {
      $data['data'] = json_encode($data['data']);
    }

    $validator = Validator::make($data, [
      'id' => [Rule::exists('survey_questions', 'id')],
      'question' => ['required', 'string', 'max:255'],
      'type' => ['required', new Enum(QuestionType::class)],
      'description' => ['nullable', 'string'],
      'data' => 'present',
      'survey_id' => [Rule::exists('surveys', 'id')],
    ]);

    $question->update($validator->validated());
  } // end of updateQuestion

} // end of class
