$(function()
{
	if($(window).width()>800)
	{
		$("#header").load("header.html");
	}
	else
	{
		$("#header").load("smallerHeader.html");
	}
});
/* This code will break the autocomplete. I think it's to do with the autocomplet function being loaded several times but not sure
$( window ).resize(function() 
{
	if($(window).width()>800)
	{
		$("#header").load("header.html");
	}
	else
	{
		$("#header").load("smallerHeader.html");
	}	
});
*/