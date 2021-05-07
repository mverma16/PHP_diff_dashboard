<template>
	<div v-if="loading" class="jumbotron">
            <p class="lead">Loading data</p>
    </div>
    <div v-else>
        <div v-if="fileList">
            <div class="row">
                <div v-if="success" class="alert alert-success" role="alert">{{success}}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div v-if="error" class="alert alert-danger" role="alert">{{error}}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
            </div>
            <div class="row">
                <div class="input-group">
                    <span class="input-group-addon" id="basic-addon1">Select Base version</span>
                    <select v-model="base_version" class="form-control">
                        <option v-for="file in fileList" :value="file.name">{{file.name}}</option>
                    </select>
                </div>
                <br>
                <div class="input-group">
                    <span class="input-group-addon" id="basic-addon1">Comapre with</span>
                    <select v-model="compare_with" class="form-control">
                        <option v-for="file in fileList" :value="file.name">{{file.name}}</option>
                    </select>
                </div>
                </div>
                <br>
                <div class="row">
                    <p><a v-on:click="starScan" class="btn btn-success pull-right" role="button">Start Scan</a></p>
                </div>
                <br>
        </div>
        <div v-else>
            <p class="lead">Oops looks like the files have not been uploaded yet.</p>
            <p>Put the version directories on server in storage/versions directory.</p>
        </div>
    </div>
</template>
<script type="text/javascript">
	import axios from 'axios'
	export default {
		data: () => {
			return {
				fileList :[],
                loading:false,
                base_version:"",
                compare_with:"",
                success:"",
                error:"",
			}
		},
        beforeMount() {
        	this.loadAvailableFileList();
        },

        methods: {
        	loadAvailableFileList() {
                this.loading=true;
        		axios.get("/api/get-version").then(response => {
            		if(response.data.success) {
                        this.fileList = response.data.data;
                    }
              		
            	}) // code to run on success
            	.catch(error => {

           	 	}).finally(() => (this.loading = false)) 
        	},

            starScan()
            {
                this.loading=true;
                let data = {};
                data["base_version"] = this.base_version;
                data["compare_with"] = this.compare_with;
                axios.post("/api/start-scan", data).then(response => {
                    this.displayTempAlert(response.data.message, "success")     
                })
                .catch(error => {
                    this.displayTempAlert(error.response.data.message, "error")
                }).finally(() => (this.loading = false))
            },

            displayTempAlert(message, type)
            {
                if(type=="success") {
                    this.success = message;
                } else {
                    this.error = message;
                }
            }
        }
	}
</script>