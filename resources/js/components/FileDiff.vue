<style scoped>
table, tr, td {
  padding: 5px;
  color: grey;
}
</style>
<template>
	<div v-if="loading" class="jumbotron">
            <p class="lead">Loading data</p>
    </div>
    <div v-else>
    	<div v-if="diffList.length">
    		<div v-for="file in diffList" class="panel panel-default">
    				<div v-for="(value, name) in file" class="panel-heading">
    					Diff in {{name}}
    					<table>
    						<tr v-for="(line, number) in value.added">
    							<td style="text-align: left;">#{{number}}</td>
    							<td>+</td>
    							<td style="color: green">{{line}}</td>
    						</tr>
    						<tr v-for="(line, number) in value.deleted">
    							<td style="text-align: left;">#{{number}}</td>
    							<td>-</td>
    							<td style="color: red">{{line}}</td>
    						</tr>
    						<tr v-for="(line, number) in value.modified">
    							<td style="text-align: left;">#{{number}}</td>
    							<td>±</td>
    							<td>Changed <span style="color: burlywood">"{{line.compared_version}}"</span> to <span style="color: burlywood">"{{line.compared_version}}"</span></td>
    						</tr>
    					</table>
    					</div>
    				</div>
    			</div>
    	</div>
    	<div v-else class="jumbotron">
			<p class="lead">Oops looks like we failed to fetch the diff.</p>
            <p>Try again later.</p>
		</div>
    </div>
</template>
<script type="text/javascript">
	import axios from 'axios'
	export default {
		data: () => {
			return {
				loading: false,
				diffType:'',
				diffList:[],
			}
		},

		beforeMount() {
			this.diffType = this.$route.name;
            this.getResult(this.$route.params.id)
        },

        methods: {
        	getResult(id)
        	{
        		let $api = this.diffType=="diffFile"?"api/get-diff-by-id/" : "api/get-diff-by-scan/";
        		this.loading=true;
        		axios.get($api+id)
                .then(response => {
                	console.log('data' in response.data);
                    if('data' in response.data) {
                    	this.diffList = response.data.data;
                    }
                //console.log(this.scanResult);
                }) // code to run on success
                .catch(error => {

                }) // code to run on error
                .finally(() => (this.loading = false))
        	}
        }
	}
</script>