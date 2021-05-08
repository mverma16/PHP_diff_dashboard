<template>
    <div v-if="loading" class="jumbotron">
            <p class="lead">Loading data</p>
    </div>
    <div v-else>
	<div v-if="scanResultData">
		<div class="jumbotron">
			<p class="lead text-left">Scan ID: #{{scanResultData.data.id}}</p>
			<div class="row">
				<ul class="col-md-6 text-left">
      				<li><b>Base Version</b> : {{scanResultData.data.base_version}}</li>
      				<li><b>Scan Type</b> : {{scanResultData.data.scan_type}}</li>

      				<li><b>Files Added/Removed</b> :  {{scanResultData.data.file_updated.length}}</li>
      				<li><b>Files Changed</b> : {{scanResultData.data.file_changed.length}}</li>
      			</ul>
      			<ul class="col-md-6 text-left">
      				<li><b>Scan Status</b> : {{scanResultData.data.is_scan_comleted? "Completed" : "Pending/Failed"}}</li>
      				<li><b>Scan Started At</b> : {{localtime(scanResultData.data.created_at)}}</li>
      				<li><b>Scan Ended At</b> : {{localtime(scanResultData.data.updated_at)}}</li>
      			</ul>
      		</div>
		</div>
		<div v-if="scanResultData.data.file_updated.length" class="row">
			<div class="col-md-12">
				<h4>Added/Removed Files List</h4>
				<ul class="nav nav-tabs">
					<li role="presentation" :class="[showList ? 'active':'']" data-toggle="tooltip" title="View as updates in base version"><a v-on:click=toggleShowList(true)><span class="glyphicon glyphicon-th-list"></span></a></li>
  					<li role="presentation" :class="[!showList ? 'active':'']" data-toggle="tooltip" title="View as comparison in both versions"><a v-on:click=toggleShowList(false)><span class="glyphicon glyphicon-th"></span></a></li>
				</ul>
				<div v-if="showList">
  					<ul class="list-group">
		  				<li v-for="file in scanResultData.data.file_updated" v-bind:class="['list-group-item', file.modification_type=='added' ? 'list-group-item-success' : 'list-group-item-danger']" data-toggle="tooltip" :title="(file.modification_type=='added'?'Found ':'Missing ')+scanResultData.data.base_version+'/'+file.path+'/'+file.filename">
  							<span v-bind:class="[file.type=='directory' ? 'glyphicon glyphicon-folder-close':'glyphicon glyphicon-file']"></span> {{file.filename}}
  						</li>
					</ul>
  				</div>
  				<div v-else>
  					<div class=" col-md-6">
  						<h5>Extra Files in {{scanResultData.data.base_version=="v1"? "v1": "v2"}}</h5>
  						<ul class="list-group">
  							<li v-for="file in addedFiles" class="list-group-item">
  								<span v-bind:class="[file.type=='directory' ? 'glyphicon glyphicon-folder-close':'glyphicon glyphicon-file']"></span> {{file.filename}}
  							</li>
  						</ul>
  					</div>
  					<div class=" col-md-6">
  						<h5>Extra Files in {{scanResultData.data.base_version!="v1"? "v1": "v2"}}</h5>
  						<ul class="list-group">
  							<li v-for="file in deletedFiles" class="list-group-item">
  								<span v-bind:class="[file.type=='directory' ? 'glyphicon glyphicon-folder-close':'glyphicon glyphicon-file']"></span> {{file.filename}}
  							</li>
  						</ul>
  					</div>
  				</div>
			</div>
		</div>
		<div v-if="scanResultData.data.file_changed.length" class="row">
			<div class="col-md-12">
				<h4>Updated Files List</h4>
                <router-link :to="{path: '/modified-files/' + scanResultData.data.id}" data-toggle="tooltip" title="Click to view diff in all modified files of this scan">
                    Show Complete Diff
                </router-link>
				<ul class="list-group">
					<li v-for="file in scanResultData.data.file_changed" class="list-group-item list-group-item-warning">
                        <router-link :to="{path: '/file-diff/' + file.id}" >
  							         <span class="glyphicon glyphicon-file"></span> {{file.file_path}}
                            <span class="glyphicon glyphicon-eye-open pull-right" data-toggle="tooltip" title="click to view diff in the file"></span>
                        </router-link>
  					</li>
				</ul>
			</div>
		</div>
	</div>
    </div>
</template>
<script type="text/javascript">
    import axios from "axios"
	export default {
		data: () => {
            return {
          	    showList: true,
                loading: false,
                scanResultData:[],
            }
        },
        computed: {
        	deletedFiles() {
        		let list =[]
        		if(!this.scanResultData && !this.scanResultData.data.file_updated.length) {
        			return list;
        		}

        		this.scanResultData.data.file_updated.map(function(value, key) {
        			if(value.modification_type=="deleted") {
        				list.push(value);
        			}
        		});
        		return list;
        	},

        	addedFiles() {
        		let list =[]
        		if(!this.scanResultData && !this.scanResultData.data.file_updated.length) {
        			return list;
        		}
        		this.scanResultData.data.file_updated.map(function(value, key) {
        			if(value.modification_type=="added") {
        				list.push(value);
        			}	
        		});
        		return list;
        	}
        },

        methods: {
        	toggleShowList($value) {
        		this.showList = $value;
        		this.scanResultData = this.scanResultData;
        	},

            getResult(id) {
                this.loading = true //the loading begin
                axios.get("/api/scan-result/"+id)
                .then(response => {
                    this.scanResultData = response.data;
                //console.log(this.scanResultData);
                }) // code to run on success
                .catch(error => {

                }) // code to run on error
                .finally(() => (this.loading = false)) 
            }
        },
        mounted: function () {
            $('[data-toggle="tooltip"]').tooltip();
        },
        beforeMount() {
            if('id' in this.$route.params) {
                this.getResult(this.$route.params.id)
            } else {
                this.scanResultData = this.scanResult
            }
        },
        props: {
			scanResult: {default:null},
		},
	}
</script>