<template>
	<div v-if="loading" class="jumbotron">
			<p class="lead">Loading data</p>
	</div>
	<div v-else>
		<div v-if="scanList.length">
			<table class="table">
      			<thead>
        			<tr>
          				<th>Scan ID</th>
          				<th>Base Version</th>
          				<th>Status</th>
          				<th>Started At</th>
          				<th>Ended At</th>
          				<th>Action</th>
        			</tr>
      			</thead>
      			<tbody>
        			<tr v-for="value in scanList">
        				<td>#{{value.id}}</td>
        				<td>{{value.base_version}}</td>
        				<td>{{value.is_scan_comleted?"Completed":"Pending/Failed"}}</td>
        				<td>{{localtime(value.created_at)}}</td>
        				<td>{{localtime(value.updated_at)}}</td>
        				<td><router-link :to="{path: '/scan-result/' + value.id}" data-toggle="tooltip" title="Click to view scan results"><span class="glyphicon glyphicon-eye-open"></span></router-link></td>
        			</tr>
			      </tbody>
		    </table>
		</div>
		<div v-else class="jumbotron">
			<p class="lead">Oops looks like the files have not been scanned yet.</p>
      		<router-link to="/process-scan"><p><button class="btn btn-lg btn-success" role="button">Scan Now</button></p></router-link>
		</div>
	</div>
</template>
<script type="text/javascript">
	import axion from "axios";
	export default {
		data: () => {
			return {
				scanList : [],
				loading: false,
			}
		},

		beforeMount() {
			this.getScanList();
		},

		mounted: function () {
            $('[data-toggle="tooltip"]').tooltip();
        },

		methods: {
			getScanList()
			{
				this.loading = true
				axios.get("/api/scans").then(response => {
            		if("data" in response.data) {
                        this.scanList = response.data.data
                    }
            	}) // code to run on success
            	.catch(error => {

           	 	}).finally(() => (this.loading = false)) 
			}
		}
	}
</script>
