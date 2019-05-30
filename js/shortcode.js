(function() {
    tinymce.create("tinymce.plugins.wpcf_button", {

        //url argument holds the absolute url of our plugin directory
        init : function(ed, url) {

            //add new button
            ed.addButton("wpcfbutton", {
                title : "Green Color Text",
                cmd : "green_command",
                image : "https://cdn3.iconfinder.com/data/icons/softwaredemo/PNG/32x32/Circle_Green.png"
            });

            //button functionality.
            ed.addCommand("green_command", function() {
                var selected_text = ed.selection.getContent();
                var return_text = "<span style='color: green'>" + selected_text + "</span>";
                ed.execCommand("mceInsertContent", 0, return_text);
            });

        },

        createControl : function(n, cm) {
            return null;
        },

        getInfo : function() {
            return {
                longname : "Extra Buttons",
                author : "Narayan Prusty",
                version : "1"
            };
        }
    });

    tinymce.PluginManager.add("wpcf_button", tinymce.plugins.wpcf_button);
})();