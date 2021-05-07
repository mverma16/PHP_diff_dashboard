<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContentUpdate extends Model
{
	protected $table = 'content_updates';
    
    protected $fillable = [
		/**
    	 * foreign key refernced to scans table
    	 */
		'scan_id',

		/**
    	 * base path of the file
    	 */
		'file_path',
		
		/**
    	 * content of base version file
    	 */
		'base_version_content',
		
		/**
    	 * content of compared version file
    	 */
		'compared_version_content',
    ];

    public function scan()
    {

    }
}
