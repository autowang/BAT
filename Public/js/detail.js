$(document).ready(function(){
	$('.bs-docs-sidenav').scrollspy();
	$(".bs-docs-sidenav li").removeClass("active");
	$(".bs-docs-sidenav li").first().addClass("active");
});