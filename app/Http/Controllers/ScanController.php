<?php

namespace App\Http\Controllers;

use App\Models\Scan;
use App\Models\FileScans;
use Illuminate\Http\Request;
use \Carbon\Carbon;
use Throwable;

/**
 * Class handles the get APIs for sending details of diff
 *
 * @category Controllers
 * @author   Manish Verma <mverma16@outlook.com>
 */
class ScanController extends Controller
{

	/**
	 *@var stores files List Of Base version => flbv
	 */
	private $flbv = [];

	/**
	 *@var stores files List Of compare with version => flcwv
	 */
	private $flcwv = [];

	/**
	 * Method scans the directories and compare them with user selected base version 
	 *
	 * @param  Illuminate\Http\Request
	 * @return Illuminate\Http\JsonResponse
	 */
	public function startScanning(Request $request)
	{
		try {
			$this->validate($request, [
    			'base_version' => 'required',
    			'compare_with' => 'required|different:base_version',
			]);
			$baseDir = $request->input('base_version', 'v2');
			$compareWith = $request->input('compare_with', 'v1');
			$scanType = $request->input('scan_type', "complete");
			if(!$this->checkGivenDirectoriesExistOnSubPaths([$baseDir,$compareWith])) {
				return errorResponse('Direcotory with base name or compare with name is missing');
			}
			$scan = Scan::create([
				'scan_type' =>  $scanType,
				'base_version' => $baseDir,
				'started_at' => Carbon::now()
			]);
			//scan files/directories for missing or added files
			$this->scanAndStoreFileUpdatesToDB($scan, $baseDir, $compareWith);
			//scan files for missing or added files
			$this->scanAndStoreContentUpdatesToDB($scan, $baseDir, $compareWith);
			$scan->update(['is_scan_comleted' => 1]);
			return successResponse('Scan completed successfully. Navigate to latest or scan list to see the results.');
		} catch(Throwable $e) {
			return errorResponse($e->getMessage());
		}
	}

	private function scanAndStoreFileUpdatesToDB($scan, string $baseDir, string $compareWith)
	{
		//scan and prepare list of base version files and dir
		$this->scanDirectoryRecursively(
			$baseDir, 
			$this->getPathWithStorage([$baseDir]),
			$this->flbv
		);
		//scan and prepare list of compared version files and dir
		$this->scanDirectoryRecursively(
			$compareWith,
			$this->getPathWithStorage([$compareWith]),
			$this->flcwv
		);
		$added = $this->getPrepareFileUpdateArray(
					// diffing  of flbv with flcwv will gives files which are added in base version
					$this->getDiffForFilesUpdates($this->flbv, $this->flcwv),
					"added"
				);
		$deleted = $this->getPrepareFileUpdateArray(
					// reverse diffing of flcwv with flbv will gives files which are deleted in compared version
					$this->getDiffForFilesUpdates($this->flcwv, $this->flbv), 
					"deleted"
				);
		$scan->fileUpdates()->createMany(array_merge($added, $deleted));
	}


	/**
	 * Method scans common files in both directories and store files content in database
	 * is content is not same for the files in both directories
	 *
	 * @param   Scan  $scan
	 * @return  void
	 */
	private function scanAndStoreContentUpdatesToDB(Scan $scan, string $baseDir, string $compareWith):void
	{
		$commonFlies = array_intersect(array_keys($this->flbv), array_keys($this->flcwv));
		foreach ($commonFlies as $file) {
			if(is_file($this->getPathWithStorage([$baseDir, $file])))
			{
				//get file contents
				$baseFileContent = file_get_contents($this->getPathWithStorage([$baseDir, $file]));
				$comparedFileContent = file_get_contents($this->getPathWithStorage([$compareWith, $file]));
				//compare file contents and store in database
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

	/**
	 * Method checks given subpaths are existing diirectories or not
	 *
	 * @param  array   $subPaths
	 * @return boolean            return false if any given subirectory does not
	 *                            exist unser storage/versions
	 */
	private function checkGivenDirectoriesExistOnSubPaths(array $subPaths):bool
	{
		foreach ($subPaths as $subPath) {
			if(!is_dir($this->getPathWithStorage([$subPath]))) return false;
		}

		return true;
	}

	/**
	 * Method formates the given file array in format to be bulk inseted in database
	 *
	 * @param   array
	 * @param   string
	 * @return  array
	 */
	private function getPrepareFileUpdateArray(array $filesArray, string $modificationType="added"):array
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

	/**
	 * Method scans given directory recursively and stores the scanned files and
	 * directories as key and their type as value in given array
	 *
	 * @param   string  $baseDir      name of parent directory being scanned
	 * @param   string  $dir          pull storage path of directory to be scanned
	 * @param   array   $fileDetails  array containing the list of files and dir found so far
	 * @return  void
	 */
	private function scanDirectoryRecursively(string $baseDir, string $dir, array &$fileDetails):void
	{
		foreach (glob($dir.DIRECTORY_SEPARATOR."*") as $file) {
			if(is_dir($file)) {
				$fileDetails[substr($file, strpos($file, $baseDir)+strlen($baseDir)+1)] = "directory";
				$this->scanDirectoryRecursively($baseDir, $file, $fileDetails);
			} else {
				$fileDetails[substr($file, strpos($file, $baseDir)+strlen($baseDir)+1)] = "file";
			}
		}
	}

	/**
	 * Method simply returns array containing the diff of given arrays by keys
	 *
	 * @param   array  $array
	 * @param   array  $arrayToCompare
	 * @return  array                   array containing keys available in $array but not in $arrayToCompare
	 */
	private function getDiffForFilesUpdates(array $array, array $arrayToCompare):array
	{
		return array_diff_key($array, $arrayToCompare);
	}

	/**
	 * Method combines the given subpaths and return resolved path with complete storage
	 * direcotry
	 *
	 * @param   array  $subPaths  array of subpaths
	 * @return  string            resolved path
	 */
	private function getPathWithStorage(array $subPaths)
	{
		$path = storage_path("versions");
		foreach ($subPaths as $subPath) {
			$path .= DIRECTORY_SEPARATOR.$subPath;
		}

		return $path;
	}
}
