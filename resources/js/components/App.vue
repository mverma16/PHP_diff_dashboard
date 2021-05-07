<template>
<div>
	<div v-if="loading" >
			<p class="lead">Loading data</p>
	</div>
	<div v-else>
		<div v-if="scanResult">
		 <scan-result v-bind:scanResult="scanResult"></scan-result>
		</div>
		<div v-else class="jumbotron">
			<p class="lead">Oops looks like the files have not been scanned yet.</p>
      <p><button class="btn btn-lg btn-success" v-on:click="makeRequest()" role="button">Scan Now</button></p>
		</div>
	</div>
</div>
</template>
<script>
    import axios from 'axios'

    export default {
        data: () => {
            return {
          	    loading: false,
          	    scanResult: null,
          	    baseURL: 'http://localhost/ladybird/experiments/diff_dashboard/public'
            }
        },

        methods: {
          makeRequest () {
            this.loading = true //the loading begin
            axios.get(this.baseURL+"/api/latest-scan-result")
            .then(response => {
            	this.scanResult = response.data;
              console.log(this.scanResult);
            }) // code to run on success
            .catch(error => {

            }) // code to run on error
            .finally(() => (this.loading = false)) // set loading to false when request finish
          }
        },

        beforeMount() {
    		this.makeRequest();
  		},

  		components: {
			'scan-result': require('./ScanResult.vue'),
		}
    }
</script>