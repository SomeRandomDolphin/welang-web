<?php

namespace App\Http\Controllers;

use App\Models\Survey;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Throwable;

class MobileSurveyController extends Controller
{
  public function entry(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'foto' => 'nullable|file|max:5120',
    ]);

    if ($validator->fails()) {
      return response()->json([
        'status' => 'error',
        'message' => 'Ukuran foto maksimal 5 MB',
        'data' => null,
      ], 400);
    }

    try {
      $survey = [];
      if ($request->foto) {
        $fotoPath = substr($request->foto->store('public/surveys'), 7);
        $survey['foto'] = $fotoPath;
      }

      $survey = Survey::create(array_merge($survey, [
        'tinggi' => $request->tinggi,
        'tanggal_kejadian' => $request->tanggal_kejadian,
        'latitude' => $request->latitude,
        'longitude' => $request->longitude,
        'user_id' => auth('api')->user()->id,
      ]));
    } catch (Throwable $e) {
      return response()->json([
        'status' => 'error',
        'message' => $e->getMessage(),
        'data' => null,
      ], 500);
    }

    $survey->tinggi = number_format($survey->tinggi, 2, '.', '');
    return response()->json([
      'status' => "success",
      'message' => "Berhasil melakukan entri survei",
      'data' => $survey
    ], 201);
  }

  public function home(Request $request)
  {
    try {
      $surveys = Survey::with('user');
      $filter['start_date'] = $request->input('start');
      $filter['end_date'] = $request->input('end');

      if ($filter['start_date']) {
        $surveys->whereDate('tanggal_kejadian', '>=', $filter['start_date']);
      }

      if ($filter['end_date']) {
        $surveys->whereDate('tanggal_kejadian', '<=', $filter['end_date']);
      } else if (!$filter['start_date']) {
        $surveys->whereDate('tanggal_kejadian', '=', Carbon::now('Asia/Jakarta')->toDateString());
      }

      $surveys = $surveys->get();
    } catch (Throwable $e) {
      return response()->json([
        'status' => 'error',
        'message' => $e->getMessage(),
        'data' => null,
      ], 500);
    }

    return response()->json([
      'status' => "success",
      'message' => "Berhasil mendapatkan data survei",
      'data' => compact('surveys', 'filter'),
    ], 200);
  }
}
