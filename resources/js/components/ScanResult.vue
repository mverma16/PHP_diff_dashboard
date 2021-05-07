<template>
    <div v-if="loading" class="jumbotron">
            <p class="lead">Loading data</p>
    </div>
    <div v-else>
	<div v-if="scanResult">
		<div class="jumbotron">
			<p class="lead text-left">Scan ID: #{{scanResult.data.id}}</p>
			<div class="row">
				<ul class="col-md-6 text-left">
      				<li><b>Base Version</b> : {{scanResult.data.base_version}}</li>
      				<li><b>Scan Type</b> : {{scanResult.data.scan_type}}</li>

      				<li><b>Files Added/Removed</b> :  {{scanResult.data.file_updated.length}}</li>
      				<li><b>Files Changed</b> : {{scanResult.data.file_changed.length}}</li>
      			</ul>
      			<ul class="col-md-6 text-left">
      				<li><b>Scan Status</b> : {{scanResult.data.is_scan_comleted? "Completed" : "Pending/Failed"}}</li>
      				<li><b>Scan Started At</b> : {{localtime(scanResult.data.created_at)}}</li>
      				<li><b>Scan Ended At</b> : {{localtime(scanResult.data.updated_at)}}</li>
      			</ul>
      		</div>
		</div>
		<div v-if="scanResult.data.file_updated.length" class="row">
			<div class="col-md-12">
				<h4>Added/Removed Files List</h4>
				<ul class="nav nav-tabs">
					<li role="presentation" :class="[showList ? 'active':'']"><a v-on:click=toggleShowList(true)><span class="glyphicon glyphicon-th-list"></span></a></li>
  					<li role="presentation" :class="[!showList ? 'active':'']"><a v-on:click=toggleShowList(false)><span class="glyphicon glyphicon-th"></span></a></li>
				</ul>
				<div v-if="showList">
  					<ul class="list-group">
		  				<li v-for="file in scanResult.data.file_updated" v-bind:class="['list-group-item', file.modification_type=='added' ? 'list-group-item-success' : 'list-group-item-danger']">
  							<a data-toggle="tooltip" title="Hooray!"><span v-bind:class="[file.type=='directory' ? 'glyphicon glyphicon-folder-close':'glyphicon glyphicon-file']"></span> {{file.filename}}</a>
  						</li>
					</ul>
  				</div>
  				<div v-else>
  					<div class=" col-md-6">
  						<h5>Extra Files in {{scanResult.data.base_version=="v1"? "v1": "v2"}}</h5>
  						<ul class="list-group">
  							<li v-for="file in addedFiles" class="list-group-item">
  								<a data-toggle="tooltip" title="Hooray!"><span v-bind:class="[file.type=='directory' ? 'glyphicon glyphicon-folder-close':'glyphicon glyphicon-file']"></span> {{file.filename}}</a>
  							</li>
  						</ul>
  					</div>
  					<div class=" col-md-6">
  						<h5>Extra Files in {{scanResult.data.base_version!="v1"? "v1": "v2"}}</h5>
  						<ul class="list-group">
  							<li v-for="file in deletedFiles" class="list-group-item">
  								<a data-toggle="tooltip" title="Hooray!"><span v-bind:class="[file.type=='directory' ? 'glyphicon glyphicon-folder-close':'glyphicon glyphicon-file']"></span> {{file.filename}}</a>
  							</li>
  						</ul>
  					</div>
  				</div>
			</div>
		</div>
		<div v-if="scanResult.data.file_changed.length" class="row">
			<div class="col-md-12">
				<h4>Updated Files List</h4>
				<ul class="list-group">
					<li v-for="file in scanResult.data.file_changed" class="list-group-item list-group-item-warning">
  							<a data-toggle="tooltip" title="Hooray!"><span class="glyphicon glyphicon-file"></span> {{file.file_path}}</a>
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
            }
        },
        computed: {
        	deletedFiles() {
        		let list =[]
        		if(!this.scanResult && !this.scanResult.data.file_updated.length) {
        			return list;
        		}

        		this.scanResult.data.file_updated.map(function(value, key) {
        			if(value.modification_type=="deleted") {
        				list.push(value);
        			}
        		});
        		return list;
        	},

        	addedFiles() {
        		let list =[]
        		if(!this.scanResult && !this.scanResult.data.file_updated.length) {
        			return list;
        		}
        		this.scanResult.data.file_updated.map(function(value, key) {
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
        		this.scanResult = this.scanResult;
        	},

            getResult(id) {
                this.loading = true //the loading begin
                axios.get("/api/scan-result/"+id)
                .then(response => {
                    this.scanResult = response.data;
                //console.log(this.scanResult);
                }) // code to run on success
                .catch(error => {

                }) // code to run on error
                .finally(() => (this.loading = false)) 
            }
        },
        
        beforeMount() {
            if('id' in this.$route.params) {
                this.getResult(this.$route.params.id)
            }
        },
        props: {
			scanResult: {default:null},
		},
	}
</script>