<?php

namespace App\Http\Controllers;

use App\Exports\HistoryExport;
use App\Models\Category;
use App\Models\Survey;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use RealRashid\SweetAlert\Facades\Alert;

class SurveyController extends Controller
{
  public function saveCategory(Request $request)
  {
    $request->validate([
      'image' => 'file|max:5120',
      'category' => 'required|integer|min:1|max:5',
      'min_height' => 'integer',
      'max_height' => 'integer',
    ]);

    try {
      if ($request->image)
        $request->image->storeAs('public/icons', "icon_$request->category.png");

      $category = Category::where('jenis', $request->category)->first();

      if (isset ($request->min_height) && isset ($request->max_height)) {
        if ($request->min_height > $request->max_height) {
          Alert::error('Gagal', 'Tinggi minimal harus di bawah atau sama dengan tinggi maksimal');
          return redirect('/')->with('failed', 'Tinggi minimal harus di bawah atau sama dengan tinggi maksimal');
        }

        $category->tinggi_maksimal = $request->max_height;
        $category->tinggi_minimal = $request->min_height;
      }

      $category->save();

      Alert::success('Sukses', 'Kategori telah berhasil diedit');
      return redirect('/')->with('success', 'Kategori telah berhasil diedit');
    } catch (Exception $e) {
      Alert::error('Gagal', 'Kategori gagal diedit');
      return redirect('/')->with('failed', 'Kategori gagal diedit');
    }
  }

  public function entry(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'tanggal_kejadian' => 'required|date',
      'tinggi' => 'required|numeric|min:0',
      'latitude' => 'required|numeric|between:-90,90',
      'longitude' => 'required|numeric|between:-180,180',
      'foto' => 'nullable|file|mimes:jpg,jpeg,png|max:5120',
    ], [
      'tanggal_kejadian.required' => 'Tanggal kejadian wajib diisi.',
      'tanggal_kejadian.date' => 'Format tanggal kejadian tidak valid.',
      'tinggi.required' => 'Tinggi genangan wajib diisi.',
      'tinggi.numeric' => 'Tinggi genangan harus berupa angka.',
      'tinggi.min' => 'Tinggi genangan tidak boleh kurang dari 0 cm.',
      'latitude.required' => 'Latitude wajib diisi. Silakan pilih lokasi pada peta.',
      'latitude.numeric' => 'Latitude harus berupa angka.',
      'latitude.between' => 'Latitude harus berada di antara -90 sampai 90.',
      'longitude.required' => 'Longitude wajib diisi. Silakan pilih lokasi pada peta.',
      'longitude.numeric' => 'Longitude harus berupa angka.',
      'longitude.between' => 'Longitude harus berada di antara -180 sampai 180.',
      'foto.file' => 'File foto tidak valid.',
      'foto.mimes' => 'Format foto harus JPG atau PNG.',
      'foto.max' => 'Ukuran foto maksimal 5 MB.',
    ]);

    if ($validator->fails()) {
      return redirect('/entry')
        ->withErrors($validator)
        ->withInput()
        ->with('failed', 'Data belum valid. Silakan periksa kembali isian Anda.');
    }

    $survey = [];
    if ($request->foto) {
      $fotoPath = substr($request->foto->store('public/surveys'), 7);
      $survey['foto'] = $fotoPath;
    } else {
      $survey['foto'] = "/";
    }


    Survey::create(array_merge($survey, [
      'tinggi' => $request->tinggi,
      'tanggal_kejadian' => $request->tanggal_kejadian,
      'latitude' => $request->latitude,
      'longitude' => $request->longitude,
      'user_id' => Auth::user()->id,
    ]));

    Alert::success('Sukses', 'Data telah berhasil dicatat');
    return redirect('/entry')->with('success', 'Entri data laporan genangan berhasil');
  }

  public function dashboard(Request $request)
  {
    $data = Survey::with('user');
    $filter['start_date'] = $request->input('start');
    $filter['end_date'] = $request->input('end');
    $filter['min_height'] = $request->input('min');
    $filter['max_height'] = $request->input('max');

    if ($filter['start_date']) {
      $data->whereDate('tanggal_kejadian', '>=', $filter['start_date']);
    }

    if ($filter['end_date']) {
      $data->whereDate('tanggal_kejadian', '<=', $filter['end_date']);
    } else if (!$filter['start_date']) {
      $currDate = Carbon::now('Asia/Jakarta')->toDateString();
      $data->whereDate('tanggal_kejadian', '=', $currDate);
      $filter['start_date'] = $currDate;
      $filter['end_date'] = $currDate;
    }

    if ($filter['min_height']) {
      $data->where('tinggi', '>=', $filter['min_height']);
    }

    if ($filter['max_height']) {
      $data->where('tinggi', '<=', $filter['max_height']);
    }

    $data = $data->get();
    $route = 'dashboard';

    $categories = Category::orderBy('jenis')->get();
    // dd($categories);
    return view('dashboard', compact('data', 'route', 'filter', 'categories'));
  }

  public function history(Request $request)
  {
    $data = Survey::with('user');
    $filter['start_date'] = $request->input('start');
    $filter['end_date'] = $request->input('end');
    $filter['search'] = $request->input('search');

    if ($filter['start_date']) {
      $data->whereDate('tanggal_kejadian', '>=', $filter['start_date']);
    }

    if ($filter['end_date']) {
      $data->whereDate('tanggal_kejadian', '<=', $filter['end_date']);
    }

    if ($filter['search']) {
      $data->whereHas('user', function ($query) use ($filter) {
        $query->where('name', 'like', '%' . $filter['search'] . '%');
      });
    }

    $data = $data->get();
    // dd($data);
    $route = 'history';
    return view('history', compact('data', 'route', 'filter'));
  }

  public function export(Request $request)
  {
    return Excel::download(
      new HistoryExport($request->input('start'), $request->input('end'), $request->input('search')),
      'survey_history_' . Carbon::now()->format('Ymd_His') . '.xlsx'
    );
  }
}
