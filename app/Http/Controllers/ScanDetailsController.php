<?php

namespace App\Http\Controllers;

use App\Models\Scan;

/**
 * Class handles the get scanning of the diles and directories
 *
 * @category Controllers
 * @author   Manish Verma <mverma16@outlook.com>
 */
class ScanDetailsController extends Controller
{
    /**
     * Method returns updated file records array linked to given Scan
     *
     * @param  Scan  $scan
     * @return Array
     */
    private function getFileUpdatesDetails(Scan $scan):array
    {
        return $this->getScanRelations(
            $scan,
            'fileUpdates',
            ['type', 'filename', 'path', 'modification_type'],
            'filename'
        );
    }

    /**
     * Method returns modified file records array linked to given Scan
     *
     * @param  Scan  $scan
     * @return Array
     */
    private function getFileContentUpdateDetails(Scan $scan):array
    {
        return $this->getScanRelations(
            $scan,
            'contentUpdates',
            ['id', 'file_path'],
            'file_path'
        );
    }

    /**
     * Function to fetch and return records of given relation
     *
     * @param  Scan    $scan
     * @param  string  $relation
     * @param  array   $getColunms
     * @param  string  $orderBy
     * @return array
     */
    private function getScanRelations(Scan $scan, string $relation, array $getColunms, string $orderBy = 'filename'):array
    {
        $relatedRecords = $scan->$relation()->orderBy($orderBy);
        if(!$relatedRecords->get()) return [];
        
        return $relatedRecords->get($getColunms)->toArray();
    }

    /**
     * Method returns the detauls of given scan 
     *
     * @param  Scan  $scan
     * @return Illuminate\Http\JsonResponse
     */
    public function getScanResults(Scan $scan)
    {
        $result = $scan->toArray();
        $result = array_merge($result, ['file_updated' => $this->getFileUpdatesDetails($scan)]);
        $result = array_merge($result, ['file_changed' => $this->getFileContentUpdateDetails($scan)]);

        return successResponse("", $result);
    }

    /**
     * Method returns the detauls of latest completed scan 
     *
     * @return Illuminate\Http\JsonResponse
     */
    public function getLatestScanResults()
    {
        if(Scan::get()->last()) {
            return $this->getScanResults(Scan::get()->last());
        }

        return errorResponse("No scans found");
    }

    /**
     * Method to get list of available versions stored in versions directory
     * 
     * @return Illuminate\Http\JsonResponse
     */
    public function getAvailbleVersionForScan()
    {
        $result = [];
        foreach(glob(storage_path("versions").DIRECTORY_SEPARATOR."*") as $version) {
            array_push($result, ["name" => basename($version)]);
        }
        return successResponse("", $result);
    }

    /**
     * Method to get list of completed scans
     * 
     * @return Illuminate\Http\JsonResponse
     */
    public function getScanList()
    {
        return successResponse("", Scan::where('is_scan_comleted',1)->get()->toArray());
    }
}
