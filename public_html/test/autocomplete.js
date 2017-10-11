$(function()
{
	$( "#searchInput" ).autocomplete({
		source: "autocomplete.php",
		minLength: 2,
	});
	$( "#resultsSearchInput" ).autocomplete({
		source: "autocomplete.php",
		minLength: 2,
	});
	$( "#searchInputHeader" ).autocomplete({
		source: "autocomplete.php",
		minLength: 2,
	});
	$( "#searchInputHeaderSmall" ).autocomplete({
		source: "autocomplete.php",
		minLength: 2,
	});
});