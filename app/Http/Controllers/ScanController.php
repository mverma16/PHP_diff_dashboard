<?php

namespace App\Http\Controllers;

use App\Models\Scan;
use App\Models\FileScans;
use Illuminate\Http\Request;
use \Carbon\Carbon;

class ScanController extends Controller
{

	/**
	 *
	 *
	 */
	private $fileListOfBaseVersion = [];
	private $fileListOfComparedVersion = [];

	public function startScanning(Request $request)
	{
		$base = $request->input('base_version', 'v2');
		$compareWith = $request->input('compare_with', 'v1');
		$scanType = $request->input('scan_type', "complete");
		// $scan = new Scan();
		$scan = Scan::create([
			'scan_type' =>  $scanType,
			'base_version' => $base,
			'started_at' => Carbon::now()
		]);
		$this->scanAndStoreFileUpdatesToDB($scan, $base, $compareWith);
		$this->scanAndStoreContentUpdatesToDB($scan);
		$scan->update(['is_scan_comleted' => 1]);
	}

	private function scanAndStoreFileUpdatesToDB($scan, string $baseDir, string $compareWith)
	{
		$this->scanDirectoryRecurrsively($baseDir, storage_path('versions').DIRECTORY_SEPARATOR.$baseDir, $this->fileListOfBaseVersion);
		$this->scanDirectoryRecurrsively($compareWith, storage_path('versions').DIRECTORY_SEPARATOR.$compareWith, $this->fileListOfComparedVersion);
		$added = $this->prepareFileUpdateArray($this->getDiffForFilesUpdates($this->fileListOfBaseVersion, $this->fileListOfComparedVersion), "added");
		$deleted = $this->prepareFileUpdateArray($this->getDiffForFilesUpdates( $this->fileListOfComparedVersion, $this->fileListOfBaseVersion), "deleted");
		$scan->fileUpdates()->createMany(array_merge($added, $deleted));
	}

	private function prepareFileUpdateArray($filesArray, $modificationType="added")
	{
		$formattedArray = [];
		foreach ($filesArray as $key => $value) {
			array_push($formattedArray, [
				'type' => $value,
			    'filename' => basename($key),
			    'path' => $key,
			    'modification_type' => $modificationType
			]);
		}

		return $formattedArray;
	}

	private function scanDirectoryRecurrsively($baseDir, $dir, &$fileDetails)
	{
		foreach (glob($dir.DIRECTORY_SEPARATOR."*") as $file) {
			if(is_dir($file)) {
				$fileDetails[substr($file, strpos($file, $baseDir)+strlen($baseDir)+1)] = "directory";
				$this->scanDirectoryRecurrsively($baseDir, $file, $fileDetails);
			} else {
				$fileDetails[substr($file, strpos($file, $baseDir)+strlen($baseDir)+1)] = "file";
			}
		}
	}

	private function getDiffForFilesUpdates($array, $arrayToCompare)
	{
		return array_diff_key($array, $arrayToCompare);
	}

	private function scanAndStoreContentUpdatesToDB(Scan $scan)
	{
		$commonFlies = array_intersect(array_keys($this->fileListOfBaseVersion), array_keys($this->fileListOfComparedVersion));
		foreach ($commonFlies as $file) {
			if(is_file(storage_path("versions").DIRECTORY_SEPARATOR."v2".DIRECTORY_SEPARATOR.$file))
			{
				$baseFileContent = file_get_contents(storage_path("versions").DIRECTORY_SEPARATOR."v2".DIRECTORY_SEPARATOR.$file);
				$comparedFileContent = file_get_contents(storage_path("versions").DIRECTORY_SEPARATOR."v1".DIRECTORY_SEPARATOR.$file);
				if($baseFileContent != $comparedFileContent) {
					$scan->contentUpdates()->updateOrCreate(["file_path" => $file],[
						"file_path" => $file,
						"base_version_content" => $baseFileContent,
						"compared_version_content" => $comparedFileContent
					]);
				}
			}
		}
	}
}