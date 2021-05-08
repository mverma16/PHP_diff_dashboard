<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\FileUpdate;
use App\Models\ContentUpdate;
class Scan extends Model
{
	protected $table = 'scans';
    
    protected $fillable = [
    	/**
    	 * Denotes type of the scan
    	 * possible vlaues:
    	 *   complete - Both file and content were scanned
    	 *   file_changes - Only files were scanned
    	 *   content_changes - Only file contents were scanned
    	 */
    	'scan_type',

    	/**
    	 * tells base version for comparison
    	 */
		'base_version',
    	
    	/**
    	 * scan comleted or not
    	 */
		'is_scan_comleted'
    ];

    public function fileUpdates()
    {
    	return $this->hasMany(
    		FileUpdate::class,
    		'scan_id'
    	);
    }

    public function contentUpdates()
    {
        return $this->hasMany(
            ContentUpdate::class,
            'scan_id'
        );
    }
}
