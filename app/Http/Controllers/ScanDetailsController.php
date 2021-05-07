<?php

namespace App\Http\Controllers;

use App\Models\Scan;

class ScanDetailsController extends Controller
{
    private function getFileUpdatesDetails(Scan $scan)
    {
        $changedFiles = $scan->fileUpdates()->orderBy('filename');
        if(!$changedFiles->get()) return  [];

        return $changedFiles->get(['type', 'filename', 'path', 'modification_type'])->toArray();
    }

    private function getFileContentUpdateDetails(Scan $scan)
    {
        $changedContents = $scan->contentUpdates()->orderBy('file_path');
        if(!$changedContents->get()) return  [];

        return $changedContents->get(['file_path'])->toArray();
    }

    public function getScanResults(Scan $scan)
    {
        $result = $scan->toArray();
        // if(in_array($request->input('scan_type', "complete"), ["complete", "file_changes"])) {
        $result = array_merge($result, ['file_updated' => $this->getFileUpdatesDetails($scan)]);
        // }

        // if(in_array($request->input('scan_type', "complete"), ["complete", "content_changes"])) {
        $result = array_merge($result, ['file_changed' => $this->getFileContentUpdateDetails($scan)]);
        // }

        return successResponse("", $result);
    }

    public function getLatestScanResults()
    {
        if(Scan::get()->last()) {
            return $this->getScanResults(Scan::get()->last());
        }

        return errorResponse("No scans found");
    }

    public function getAvailbleVersionForScan()
    {
        $result = [];
        foreach(glob(storage_path("versions").DIRECTORY_SEPARATOR."*") as $version) {
            array_push($result, ["name" => basename($version)]);
        }
        return successResponse("", $result);
    }

    public function getScanList()
    {
        return successResponse("", Scan::get()->toArray());
    }
}
