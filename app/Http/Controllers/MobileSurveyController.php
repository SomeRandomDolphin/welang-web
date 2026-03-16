<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Survey;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Throwable;

class MobileSurveyController extends Controller
{
    /**
     * Submit a new flood survey entry from the mobile app.
     * POST /api/mobile/entry
     */
    public function entry(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tinggi'            => 'required|numeric|min:0',
            'tanggal_kejadian'  => 'required|date',
            'latitude'          => 'required|numeric|between:-90,90',
            'longitude'         => 'required|numeric|between:-180,180',
            'foto'              => 'nullable|file|mimes:jpg,jpeg,png|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => $validator->errors()->first(),
                'data'    => null,
            ], 422);
        }

        try {
            $surveyData = [
                'tinggi'           => $request->tinggi,
                'tanggal_kejadian' => $request->tanggal_kejadian,
                'latitude'         => $request->latitude,
                'longitude'        => $request->longitude,
                'user_id'          => auth('api')->user()->id,
            ];

            if ($request->hasFile('foto')) {
                $fotoPath = substr($request->foto->store('public/surveys'), 7);
                $surveyData['foto'] = $fotoPath;
            }

            $survey = Survey::create($surveyData);

        } catch (Throwable $e) {
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage(),
                'data'    => null,
            ], 500);
        }

        $survey->tinggi = number_format($survey->tinggi, 2, '.', '');

        return response()->json([
            'status'  => 'success',
            'message' => 'Berhasil melakukan entri survei',
            'data'    => $survey,
        ], 201);
    }

    /**
     * Get flood surveys with optional date filtering.
     * GET /api/mobile/surveys?start=YYYY-MM-DD&end=YYYY-MM-DD
     */
    public function surveys(Request $request)
    {
        try {
            $query = Survey::with('user');

            $startDate = $request->input('start');
            $endDate   = $request->input('end');

            if ($startDate) {
                $query->whereDate('tanggal_kejadian', '>=', $startDate);
            }

            if ($endDate) {
                $query->whereDate('tanggal_kejadian', '<=', $endDate);
            } elseif (!$startDate) {
                // Default: show today's surveys if no filter given
                $query->whereDate('tanggal_kejadian', '=', Carbon::now('Asia/Jakarta')->toDateString());
            }

            $surveys = $query->latest('tanggal_kejadian')->get();

        } catch (Throwable $e) {
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage(),
                'data'    => null,
            ], 500);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Berhasil mendapatkan data survei',
            'data'    => [
                'surveys'    => $surveys,
                'start_date' => $startDate ?? null,
                'end_date'   => $endDate ?? null,
            ],
        ], 200);
    }

    /**
     * Get all flood categories (icons, min/max heights).
     * GET /api/mobile/categories
     * Used by Flutter to color-code map markers.
     */
    public function categories()
    {
        try {
            $categories = Category::orderBy('jenis')->get();
        } catch (Throwable $e) {
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage(),
                'data'    => null,
            ], 500);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Berhasil mendapatkan data kategori',
            'data'    => $categories,
        ], 200);
    }
}