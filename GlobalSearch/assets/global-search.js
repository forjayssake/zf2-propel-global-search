"use strict";

$(function(){
	
	$('#global-search-text').on('keyup', function(){
		var $searchValue = $(this).val();
		if ($searchValue.length > 1)
		{
			$('#global-search').prop('disabled', false);
		} else {
			$('#global-search').prop('disabled', true);
		}
	});
	
	$('#global-search').on('click', function(event){
		
		event.preventDefault();
		
		var $searchValue = $('#global-search-text').val();
		globalSearch($searchValue);
	});
});


function globalSearch(searchValue)
{
	var $modal = $('#GlobalSearchPleaseWaitModal');
	$modal.find('.gs-total-rows').html('');
	$modal.find('div.gs-search-error').hide();
	
	var $totalRows = 0;
	
	setModalSearching();
	$modal.modal('show');
	
	var $action = $('#GlobalSearchForm').attr('action');

	$modal.find('.global-search-no-results').find('span.result').html('');

	$.ajax({
	    url: $action,
	    type: 'POST',
	    dataType: 'json',
	    async: true,
	    data: {'searchvalue': searchValue},
	    success: function (data) {
	    	setModalData(data);
	    },
	    error: function (data) {
	        setModalError();
	    }
	});


	function setModalSearching()
	{
		$modal.find('div.modal-header').find('h3').html('Please Wait');
		$modal.find('.globalSearchResultsWrapper').hide();
		$modal.find('.global-search-no-results').hide();
		$modal.find('.generic-modal-waiting').show();
	}


	function setModalResults(content, selectOptions, totalRows)
	{
		$modal.find('div.modal-header').find('h3').html('<i class="fa fa-search"></i> Search Results:');
		$modal.find('.generic-modal-waiting').hide();
		if (totalRows > 0)
		{
			$modal.find('.gs-total-rows').html(' <span id="gs-total">' + totalRows + '</span>' + (totalRows == 1 ? ' Item' : ' Items') + ' found for: <span class="bold">' + $('#global-search-text').val() + '</span>' + selectOptions);
			
			var $contentWrapper = $modal.find('.globalSearchResultsWrapper');
			$contentWrapper.html(content);
			$contentWrapper.show();
			
		} else {
			$modal.find('.global-search-no-results').find('span.result').append('<span class="bold">' + $('#global-search-text').val() + '</span>');
			$modal.find('.global-search-no-results').show();
		}
	}
	
	
	function setModalError()
	{
		$modal.find('.generic-modal-waiting').hide();
		$modal.find('div.gs-search-error').show();
	}


	function setModalData(data)
	{
		
		var $tableContent 	= '<table id="global-search-result-table" class="table table-condensed table-striped table-hover table-bordered table-propel ">';
		
		var keys = Object.keys(data.SearchResult);
		
		var selectOptions = null;
		var showSelect = false;
		if (keys.length > 1)
		{
			showSelect = true;
			var selectItems = new Array();
			var $selectContent 	= '<div class="global-search-select-wrapper"><select id="global-search-select"><option value="all">Show All</option>';
		}
		
		for (var i = 0; i < keys.length; i++) {
			
			var includeHeader = true;
			
			$.each(data.SearchResult[keys[i]], function(index, element) {
				
				if (includeHeader)
				{
					$tableContent = $tableContent + '<tr class="global-search-' + keys[i] + ' header">';
						$tableContent = $tableContent + '<td><i class="fa ' + element.icon + '"></i></td>';
						$tableContent = $tableContent + '<td><strong>' + element.displayObject + '</strong></td>';
						$tableContent = $tableContent + '<td colspan="2"><strong>' + element.displayFields + '</strong></td>';
					$tableContent = $tableContent + '</tr>';
					
					includeHeader = false;
				}
				
				if (showSelect && $.inArray(keys[i], selectItems) == -1)
				{
					selectItems.push(keys[i]);
					$selectContent = $selectContent + '<option value="' + keys[i] + '">' + element.displayObject + '</option>';
				}
				
				$tableContent = $tableContent + '<tr class="global-search-' + keys[i] + '">';
					$tableContent = $tableContent + '<td><i class="fa ' + element.icon + '"></i></td>';
					$tableContent = $tableContent + '<td>' + element.displayObject + '</td>';
					$tableContent = $tableContent + '<td>' + element.resultString + '</td>';
					$tableContent = $tableContent + '<td><a class="btn btn-primary btn-xs" href="' + element.url + '">View</td>';
				$tableContent = $tableContent + '</tr>';
				
				$totalRows++;
			});
			
		}
		
		$selectContent = $selectContent + '</select></div>';
		$tableContent = $tableContent + '</table>';
		
		setModalResults($tableContent, $selectContent, $totalRows);
	}


	$('body').on('change', '#global-search-select', function() {
		
		var value = $(this).val();
		if (value == 'all')
		{
			$('#global-search-result-table').find('tr').show();
			$('#gs-total').html($totalRows);
		} else {
			$('#global-search-result-table').find('tr').hide();
			$('#global-search-result-table').find('tr.global-search-' + value).show();
			$('#gs-total').html($('#global-search-result-table').find('tr.global-search-' + value).length - 1 ); // exclude the section header from the count
		}
		
	});
	
};