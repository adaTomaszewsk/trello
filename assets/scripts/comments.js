$(document).ready(function() {
    $("form").on("submit", function(e) {
        tinyMCE.triggerSave(); 
    });
});
