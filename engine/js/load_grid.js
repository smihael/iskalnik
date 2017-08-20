/*
 * Editablegrid
 * Copyright (c) 2011 Webismymind SPRL
 * Dual licensed under the MIT or GPL Version 2 licenses.
 * 
 * Spletne podatkovne baze za FF UNI LJ
 * Copyright (c) 2013 Mihael Simonic
 * Na voljo pod CC-BY-SA 3.0 licenco
 * 
 */

// create our editable grid

var name = document.getElementById("baza").getAttribute("data-name");


var editableGrid = new EditableGrid(name, {
	enableSort: true,  
	editmode: "absolute", 
	editorzoneid: "edition", 
	pageSize: 10,
});


// helper function to display a message
function displayMessage(text, style) { 
	_$("message").innerHTML = "<p class='" + (style || "ok") + "'>" + text + "</p>"; 
	console.log(text);
        
        setTimeout(function() {
            $('#message').fadeOut('slow');
        }, 2000)
} 


// helper function to get path of a demo image
function image(relativePath) {
	return "images/" + relativePath;
}

// this will be used to render our table headers
function InfoHeaderRenderer(message) { 
	this.message = message; 
	this.infoImage = new Image();
	this.infoImage.src = image("information.png");
};

InfoHeaderRenderer.prototype = new CellRenderer();
InfoHeaderRenderer.prototype.render = function(cell, value) 
{
	if (value) {
		var link = document.createElement("a");
		link.href = "javascript:alert('" + this.message + "');";
		link.appendChild(this.infoImage);
		cell.appendChild(document.createTextNode("\u00a0\u00a0"));
		cell.appendChild(link);
	}
};

// this function will initialize our editable grid
EditableGrid.prototype.initializeGrid = function() 
{
	with (this) {

		// use a special header renderer to show an info icon for some columns
		
		modelChanged = function(rowIndex, columnIndex, oldValue, newValue, row) { 
			displayMessage("Polje '" + this.getColumnName(columnIndex) + "' v vrstici " + this.getRowId(rowIndex) + " je bilo spremenjeno iz '" + oldValue + "' v '" + newValue + "'");
			updateCellValue(this, rowIndex, columnIndex, oldValue, newValue, row);
		};
		
		// update paginator whenever the table is rendered (after a sort, filter, page change, etc.)
		tableRendered = function() { this.updatePaginator(); };

		//rowSelected = function(oldRowIndex, newRowIndex) {
		//	if (oldRowIndex < 0) displayMessage("Izbrana vrstica '" + this.getRowId(newRowIndex) + "'");
		//	else displayMessage("Izbrana vrstica se je spremenila iz '" + this.getRowId(oldRowIndex) + "' na '" + this.getRowId(newRowIndex) + "'");
		//};
		
		// render for the action column
		setCellRenderer("action", new CellRenderer({render: function(cell, value) {
			var rowId = editableGrid.getRowId(cell.rowIndex);
			cell.innerHTML = '<button class="ui-state-default ui-corner-all btn" data-toggle="modal" data-target="#podrobnosti-modal" onclick="$( \'#podrobnosti-modal #modal-content\').load(\'../engine/show_details_table.php?baza='+ name +'&id='+ rowId +'\');"><span class="ui-icon ui-icon-info" alt="info" title="Prikaži podrobnosti"></span></button>';
		}})); 

		
		// render the grid (parameters will be ignored if we have attached to an existing HTML table)
		renderGrid("tablecontent", name + " table");
		
		// set active (stored) filter if any
		_$('filter').value = currentFilter ? currentFilter : '';
		
		// filter when something is typed into filter
		_$('filter').onkeyup = function() { editableGrid.filter(_$('filter').value); };
		
		// bind page size selector
		$("#pagesize").val(pageSize).change(function() { editableGrid.setPageSize($("#pagesize").val()); });
		//$("#barcount").val(maxBars).change(function() { editableGrid.maxBars = $("#barcount").val(); editableGrid.renderCharts(); });
	}
};

EditableGrid.prototype.onloadXML = function(url) 
{
	// register the function that will be called when the XML has been fully loaded
	this.tableLoaded = function() { 
		displayMessage("Podatkovna zbirka je naložena prek XML: " + this.getRowCount() + " vrstic");
		this.initializeGrid();
	};
	
	// load XML URL
	this.loadXML(url);
	
	this.loaded = true;
	
};


// function to render the paginator control
EditableGrid.prototype.updatePaginator = function()
{
	var paginator = $("#paginator").empty();
	var nbPages = this.getPageCount();


	// get interval
	var interval = this.getSlidingPageInterval(30);
	if (interval == null) return;
	
	// get pages in interval (with links except for the current page)
	var pages = this.getPagesInInterval(interval, function(pageIndex, isCurrent) {
		if (isCurrent) return $("<li>").addClass("active").append($("<a>").html(pageIndex + 1));
		return $("<li>").append($("<a>").css("cursor", "pointer").html(pageIndex + 1).click(function(event) { editableGrid.setPageIndex(parseInt($(this).html()) - 1); })) ;
	});

	// "prev" link
	link = $("<a>").html("&laquo;");
	if (!this.canGoBack()) 
		paginator.append(($("<li>").addClass("disabled").append(link)));	
	else
		paginator.append($("<li>").append(link.css("cursor", "pointer").click(function(event) { editableGrid.prevPage(); })));

	// pages
	for (p = 0; p < pages.length; p++) {
		paginator.append((pages[p]));
	}
	
	// "next" link
	link = $("<a>").html("&raquo;");
	if (!this.canGoForward())
		paginator.append(($("<li>").addClass("disabled").append(link)));
	else 
		paginator.append($("<li>").append(link.css("cursor", "pointer").click(function(event) { editableGrid.nextPage(); })));

};

function updateCellValue(editableGrid, rowIndex, columnIndex, oldValue, newValue, row, onResponse)
{      
}

NumberCellEditor.prototype.editorValue = function(value) {
	return isNaN(value) ? "" : (value + '').replace(',', this.column.decimal_point);
};
NumberCellEditor.prototype.getEditorValue = function(editorInput) {
	return editorInput.value.replace('.', ',');
};

function TextareaEditor(config) { 
	this.minWidth = 75; 
	this.minHeight = 22; 
	this.adaptHeight = true; 
	this.adaptWidth = true;
	this.init(config); 
}

TextareaEditor.prototype = new CellEditor();
TextareaEditor.prototype.getEditor = function(element, value)
{
	// create select list
	var htmlInput = document.createElement("textarea");
	htmlInput.setAttribute("style", "overflow:visible; z-index: 999;");

	previousValue = this.editorValue(value);
	htmlInput.value = previousValue;
	
	// auto adapt dimensions to cell, with a min width
	if (this.adaptWidth) htmlInput.style.width = Math.max(this.minWidth, this.editablegrid.autoWidth(element)) + 'px'; 
	if (this.adaptHeight) htmlInput.style.height = Math.max(this.minHeight, this.editablegrid.autoHeight(element)) + 'px';
	
       
	// when a new value is selected we apply it
	htmlInput.onchange = function(event) { this.onblur = null; this.celleditor.applyEditing(this.element, this.value,previousValue); };
	
	return htmlInput; 
};
TextareaEditor.prototype.editorValue = function(value) {
	return value;
};
TextareaEditor.prototype.getEditorValue = function(editorInput) {
	return editorInput.value;
};
TextareaEditor.prototype.applyEditing = function(element, newValue, previousValue) 
{
	with (this) {
		if (element && element.isEditing) {
			if (!column.isValid(newValue)) return false;
			
			var newValue = editableGrid.setValueAt(element.rowIndex, element.columnIndex, newValue);
// 			if (!editableGrid.isSame(newValue, previousValue)) {
			editableGrid.modelChanged(element.rowIndex, element.columnIndex, previousValue, newValue, editableGrid.getRow(element.rowIndex));
// 			}
			_clearEditor(element);	
		}
	}
};
