<?php

namespace App\Http\Controllers;

use App\Models\Scan;
use App\Models\ContentUpdate;
use Illuminate\Http\Request;
use Throwable;

/**
 * Class handles diff result for modified files
 *
 * @category Controllers
 * @author   Manish Verma <mverma16@outlook.com>
 */
class FileDiffController extends Controller
{

	/**
	 * Method iterates over ContentUpdate linked to Scan and
	 * returns the modified file content details containing modification
	 * type, line number and content in JSON response
	 *
	 * @param   Scan  $scan
	 * @return  Illuminate\Http\JsonResponse
	 */
	public function getDiffOfScan(Scan $scan)
	{
		$result = [];
		foreach ($scan->contentUpdates()->get() as $scanData) {
			array_push($result, $this->getFileDiffDetails($scanData));
		}

		return successResponse("", $result);
	}

	/**
	 * Method returns the modified file content details containing modification
	 * type, line number and content in JSON response
	 *
	 * @param   Scan  $scan
	 * @return  Illuminate\Http\JsonResponse
	 */
	public function getDiffOfFile(ContentUpdate $contentUpdate)
	{
		return successResponse("", [$this->getFileDiffDetails($contentUpdate)]);
	}

	/**
	 * Method compares the content of base version and compared verion in given
	 * $contentUpdate and returns the formatted array containing details like file name,
	 * added lines, removed lines and modified lines in base version.
	 *
	 * @param   ContentUpdate $contentUpdate
	 * @return  arrat                         empty array or ['filename' =>[
	 															'added'=>[
	 															    linenumber => linecontent
	 																....
	 															],
	 															"deleted"=>[
	 																linenumber => linecontent
	 																....
	 															],
	 															"modiefied"=>[
	 																linenumber =[
																		"base_version" => linecontent,
																		"compared_version"=> linecontent,
	 																]
	 																...
	 															]
	 														]]
	 */
	private  function getFileDiffDetails(ContentUpdate $contentUpdate):array
	{
		$result = [];
		$i=0;
		$base = $this->getFileContentLinesAsArray($contentUpdate->base_version_content);
		$compared = $this->getFileContentLinesAsArray($contentUpdate->compared_version_content);
		$added = $deleted = $modified = [];
		$added = array_diff($base, $compared);
		$deleted = array_diff($compared, $base);
		$commonKeys = array_intersect(array_keys($added), array_keys($deleted));
		foreach ($commonKeys as $value) {
				$modified[$value] = ["base_version" => $added[$value], "compared_version" => $deleted[$value]];
			unset($added[$value], $deleted[$value]);
		}
		$result[$contentUpdate->file_path] = [
			"added" => $added,
			"deleted" => $deleted,
			"modified" => $modified
		];

		return $result;
	}

	/**
	 * Method iterates over each line of the saved cotent of the file considering
	 * each file will contain \n as end of line and returns an array of line number
	 * and line content
	 *
	 * @param  string  $fileContent (can very large string)
	 * @return array                empty array or [linenumber => linecontent,...]
	 */
	private function getFileContentLinesAsArray($fileContent):array
	{
		$i=1;
		$contentArray=[];
		foreach(preg_split("/((\r?\n)|(\r\n?))/", $fileContent) as $line){
			$key = (string)$i;
    		$contentArray[$key] = $line;
    		$i++;
		}
		return $contentArray;
	}
}
