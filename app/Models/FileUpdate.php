<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FileUpdate extends Model
{
	protected $table = 'file_updates';
    
    protected $fillable = [
		/**
    	 * foreign key refernced to scans table
    	 */
		'scan_id',

		/**
    	 * Type of the missing/new file possible values:
    	 * file or directory
    	 */
		'type',

		/**
    	 * Name of the changed file
    	 */
		'filename',

		/**
    	 * Path of the changed file
    	 */
		'path',

		/**
    	 * Type of modification possible values
    	 * added or deleted
    	 */
		'modification_type'
    ];

    public function scan()
    {

    }
}