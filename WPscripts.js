var editorBookMark = null;

var insertionMode=null;
		
var n3_myField=null;
var n3_sel=null;
var n3_startPos=null;
var n3_endPos =null;

jQuery(window).resize(function() {
    jQuery("#modal").css("width", (jQuery(window).width() - 80 )+"px");
    jQuery("#modal").css("height", (jQuery(window).height() - 80 )+"px");
    jQuery("#detailsIframe").css("height", (jQuery(window).height() - 85 )+"px");

    n3_modal_object.center();
	
});

var n3_modal_object = (function () {
    var 
    method = {};

    // Center the modal in the viewport
    method.center = function () {
             
        var top, left;

        top = Math.max(jQuery(window).height() - jQuery(n3_modal).outerHeight(), 0) / 2;
        left = Math.max(jQuery(window).width() - jQuery(n3_modal).outerWidth(), 0) / 2;

        jQuery("#modal").css("top", top+"px");
        jQuery("#modal").css("left", left+"px");


    };

    // Open the modal
    method.open = function (contentToRender,mode) {
              
        insertionMode=mode;

        jQuery(n3_modal).css("width", (jQuery(window).width() - 80 )+"px");
        jQuery(n3_modal).css("height", (jQuery(window).height() - 80 )+"px");
				

        jQuery("#detailsIframe").css("height", (jQuery(window).height() - 85 )+"px");

        jQuery(n3_modal).show();
        jQuery("#wpadminbar").css("z-index",9997);

              


        var salida=contentToRender.replace(/@@line@@/g,"\n");
        salida=salida.replace(/&lt;/g,"<");
				
        if (insertionMode=="html")	
        {
            n3_myField=jQuery("#content")[0];
            //IE support
            if (document.selection) {
                n3_myField.focus();
                n3_sel = document.selection.createRange();			
            }
            //MOZILLA/NETSCAPE support
            else if (n3_myField.selectionStart || n3_myField.selectionStart == '0') {
                n3_startPos = n3_myField.selectionStart;
                n3_endPos = n3_myField.selectionEnd;			
            } 
        }
        jQuery('#detailsIframe').html(salida);

        method.center();
        //jQuery(window).bind('resize.modal', method.center);

        n3_overlay.show();
    };

    // Close the modal
    method.close = function () {
        //jQuery('#detailsIframe').attr('src', 'about:blank');
        jQuery(n3_modal).hide();
        jQuery('#detailsIframe').html('');
        jQuery(n3_overlay).hide();
    //jQuery(window).unbind('resize.modal');
    };

           
    // Generate the HTML and add it to the document
    var n3_overlay = jQuery('<div id="overlay"></div>');
    var n3_modal = jQuery('<div id="modal"></div>');
    var n3_content = jQuery('<div id="content"> <div name="detailsIframe" id="detailsIframe"  frameborder="0" marginheight="0" marginwidth="0"  style=" overflow-x:hidden;overflow-y: auto;"></div></div>');
    var n3_close = jQuery('<a id="close" href="#">close</a>');
		
			
    jQuery(n3_modal).hide();
    jQuery(n3_overlay).hide();
    jQuery(n3_modal).append(jQuery(n3_content), jQuery(n3_close));

    jQuery(document).ready(function () {
        jQuery('body').append(jQuery(n3_overlay), jQuery(n3_modal));
    });

    jQuery(n3_close).click(function (e) {
        e.preventDefault();
        method.close();
    });

    return method;
} ());

function n3InsertAtCursor( myValue) {


    if (insertionMode=="html")
    {
        //IE support
        if (document.selection) {
            n3_sel.text = myValue;
        }
        //MOZILLA/NETSCAPE support
        else if (n3_myField.selectionStart || n3_myField.selectionStart == '0') {
		
            n3_myField.value = n3_myField.value.substring(0, n3_startPos)
            + myValue 
            + n3_myField.value.substring(n3_endPos, n3_myField.value.length);
        } else {
            n3_myField.value += myValue;
        }
		  
    }
    if (insertionMode=="visual")
    {
        tinyMCE.activeEditor.selection.moveToBookmark(editorBookMark);
        tinyMCE.execCommand('mceInsertContent',false,myValue);
    }
		 
		 
    insertionMode=null;
    try
    {
        n3_modal_object.close(); 
    }catch(e)
    {
			 
    }
		 
}



function  n3PostVideoHandler()
{
    n3_modal_object.open(n3videoItems,"html");
}			

