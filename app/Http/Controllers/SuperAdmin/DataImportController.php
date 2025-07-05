<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Services\Import\CrackersDataImporter;
use App\Models\SuperAdmin\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class DataImportController extends Controller
{
    /**
     * Display the import interface
     */
    public function index()
    {
        $companies = Company::orderBy('name')->get();
        return view('super-admin.data-import.index', compact('companies'));
    }

    /**
     * Preview data from HTML content
     */
    public function preview(Request $request)
    {
        $request->validate([
            'html_content' => 'required|string',
            'company_id' => 'nullable|exists:companies,id'
        ]);

        try {
            $company = null;
            if ($request->company_id) {
                $company = Company::find($request->company_id);
            }

            $importer = new CrackersDataImporter($company);
            
            // Parse HTML content
            $importer->parseHtmlContent($request->html_content);
            $stats = $importer->getStatistics();

            return response()->json([
                'success' => true,
                'stats' => $stats
            ]);

        } catch (\Exception $e) {
            Log::error('Error previewing import data: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Import data from HTML content
     */
    public function import(Request $request)
    {
        $request->validate([
            'html_content' => 'required|string',
            'company_id' => 'nullable|exists:companies,id'
        ]);

        try {
            $company = null;
            if ($request->company_id) {
                $company = Company::find($request->company_id);
            }

            $importer = new CrackersDataImporter($company);
            $result = $importer->importFromHtml($request->html_content);

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => 'Data imported successfully!',
                    'result' => $result
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'error' => $result['error']
                ], 500);
            }

        } catch (\Exception $e) {
            Log::error('Error importing data: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload and import from file
     */
    public function uploadAndImport(Request $request)
    {
        $request->validate([
            'html_file' => 'required|file|mimes:html,htm,txt|max:10240',
            'company_id' => 'nullable|exists:companies,id'
        ]);

        try {
            // Read file content
            $file = $request->file('html_file');
            $htmlContent = file_get_contents($file->getRealPath());

            if (empty($htmlContent)) {
                return response()->json([
                    'success' => false,
                    'error' => 'File is empty or could not be read'
                ], 400);
            }

            $company = null;
            if ($request->company_id) {
                $company = Company::find($request->company_id);
            }

            $importer = new CrackersDataImporter($company);
            $result = $importer->importFromHtml($htmlContent);

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => 'Data imported successfully from file!',
                    'result' => $result
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'error' => $result['error']
                ], 500);
            }

        } catch (\Exception $e) {
            Log::error('Error importing from file: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get import history
     */
    public function history()
    {
        try {
            // Get recent imports from logs or database
            $imports = []; // This would come from a proper import log table
            
            return response()->json([
                'success' => true,
                'imports' => $imports
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching import history: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
