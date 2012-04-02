<script type="text/javascript">
$().ready(function() {
    var auto_refresh = setInterval(
    function () {
	$('#badgeNotifications a').load('{devblocks_url}c=cerb5blog.autorefresh.notifications&a=getUnreadNotifications{/devblocks_url}').fadeIn("slow");
    }, 60000); // refresh every 60000 milliseconds or 60 seconds
});
</script>