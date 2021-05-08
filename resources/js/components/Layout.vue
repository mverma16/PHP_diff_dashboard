<template>
	<div class="container">
		<div class="header clearfix">
            <nav>
                <ul class="nav nav-pills pull-right">
                    <li role="presentation">
                        <router-link to="/" >Latest</router-link>
                    </li>
                    <li role="presentation">
                        <router-link to="/scan-list" >Scan List</router-link>
                    </li>
                    <li role="presentation">
                        <router-link to="/process-scan">Scan Now</router-link>
                    </li>
                </ul>
            </nav>
            <h3 class="text-muted">Diff Dashoard</h3>
  		</div>
        <router-view></router-view>
		<footer class="footer">
            <p>&copy; 2021 copyright | Epicate, IND.</p>
        </footer>
	</div>
</template>
<script type="text/javascript">
	export default {
        // mounted: function () {
        //     $('[data-toggle="tooltip"]').tooltip();
        //     alert('here');
        // }
	}
</script>