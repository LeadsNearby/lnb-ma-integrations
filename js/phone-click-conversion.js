

jQuery(document).ready(function($) {
	
	$("a[href^='tel']").on('click', function() {
        var mycookies = {};
		var temp = document.cookie.split(";");
		var key  = "";
		var val  = "";
		
    $.urlParam = function(name){
        var results = new RegExp('[\?&]' + name + '=([^]*)').exec(window.location.href);
        if (results === null){
            return null;
        } else{
            return results[1] || 0;
        }
    }
		
		// var cID = newURL.split("CID=")
		for(i=0;i<temp.length;i++){
			key = temp[i].split("=")[0].trim(); // added trim here
			val = temp[i].split("=")[1];
			mycookies[key] = val;
		}
        
        $.post(
            phone_num_ajax.ajaxurl,
            {
            'action': 'number_click',
            'data': {  
                Cookies:  mycookies, 
                CID: $.urlParam ('CID') 
            },
            }, 
            
            function(response){
                // console.log('The server responded: ' + response);
                // console.log(mycookies);
            }
        );
            
        });

    
});