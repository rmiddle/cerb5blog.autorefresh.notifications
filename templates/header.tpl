<script type="text/javascript">
$().ready(function() {
    var auto_refresh = setInterval(
    function () {
	$('#badgeNotifications a').load('{devblocks_url}c=cerb5blog.autorefresh.notifications&a=getUnreadNotifications{/devblocks_url}').fadeIn("slow");
    }, {$refreshrate}000); // refresh every 1000 milliseconds = 1 seconds
});
</script>
