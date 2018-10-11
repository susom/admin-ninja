$(document).ready( function() {
    // Fix hotkeys
    if (hotkeys) {
        //TO ACTIVATE SHORTCUTS ON TEXTAREA ANS INPUT
        hotkeys.filter = function (event) {
            var tagName = (event.target || event.srcElement).tagName;
            return !(tagName.isContentEditable || tagName == 'INPUT' || tagName == 'SELECT' || tagName == 'TEXTAREA');
        };

        hotkeys.filter = function (event) {
            var tagName = (event.target || event.srcElement).tagName;
            hotkeys.setScope(/^(INPUT|TEXTAREA|SELECT)$/.test(tagName) ? 'input' : 'other');
            return true;
        };
    }

    // Bind Hotkey Events
    if (hotkeys) {
        hotkeys('shift+space,ctrl+shift+a,ctrl+shift+c,ctrl+shift+r,ctrl+shift+z', function(event,handler) {
            switch(handler.key){
                case "shift+space":SAN.ToggleConsole();break;
                case "ctrl+shift+a":SAN.AddTestRecord();break; //new record
                case "ctrl+shift+c":SAN.GoToCodeBook();break; //data dictionary
                case "ctrl+shift+r":SAN.GoToOnlineDesigner();break; // new report
                case "ctrl+shift+z":SAN.GoToCustom('http://www.google.com');break; //online designer
            }
        });
    }

    SAN.init();
});



// Data Cleanup javascript class
var SAN = SAN || {};

SAN.init = function() {

    // Handle enter in searchbox
    $(".shell-searchbox").keypress(SAN.handleEnter);


    // Close console on click outside
    $(document).mouseup(function (e) {

        SAN.container = $('#shell-wrap');

        // if the target of the click isn't the container nor a descendant of the container
        if (!SAN.container.is(e.target) && SAN.container.has(e.target).length === 0)
        {
            SAN.HideConsole();
        }

    });

    // Const for textarea element
    SAN.textarea = $('#san_textarea');

    // Enable mirroring
    SAN.textarea.on('keyup', function() {
        SAN.textareaMirror();
    });

    // Bind buttons
    // $('.container').on('click', 'button', dc.buttonPress);



    // Append the search box
    SAN.searchElement = $('<select id="san_nav_search"></select>');

    var search_container = $('<div class="san_nav_search_container"></div>')
        .append(SAN.searchElement);

    var icon = $('<i class="ninja-icon fas fa-user-ninja"></i>');
    var topbar = $('<div/>')
        .append(icon)
        .append(search_container)
        .addClass('ninja-bar');







    //     .append(icon)
    //     .append(SAN.searchElement)
    //
    //     .prependTo('#pagecontent');
    //     // .addClass('nav-link')
    //     // .insertBefore($('#redcap-home-navbar-collapse ul:first li:first'));

    SAN.searchElement.select2({
        allowClear: true,
        width: '100%',
        // minimumInputLength: 1,
        ajax: {
            url: SAN.ajax_endpoint,
            dataType: 'json',
            delay: 250,         // wait 250ms before trigging ajax call
            cache: true,
            // data: function(params){
            //     var query = {
            //         search: params.term,
            //         type: 'getAllProjects'
            //     };
            //     return query;
            // },
            processResults: function (data) {
                return {
                    results: data.results
                };
            }
        },
        placeholder: 'Ninja It',
    }).bind('change',function() {

        var active = $(this).val();

        console.log( "Val Changed", this, active);

    });





    // Add toolbar to project pages
    if ( $('#west:visible')) {
        console.log("HI");
        $('.mainwindow').prepend(topbar);
    }

    // Add toolbar to navbar pages
    if ( $('#redcap-home-navbar-collapse').is(':visible') ) {
        $('#pagecontent').prepend(topbar);
    }



};



// Using Andy123 autofill bookmarklet https://github.com/123andy/redcap-autofill-bookmarklet
SAN.AutofillForms = function() {
    var jsCode = document.createElement('script');
    jsCode.setAttribute('src', 'https://med.stanford.edu/webtools/redcap/redcap_auto_fill.js');
    document.body.appendChild(jsCode);
    SAN.HideConsole();
};




SAN.GoToCodeBook = function() {
    if (pid > 0) window.location.href = app_path_webroot + "Design/data_dictionary_codebook.php?pid=" + pid;
};

SAN.GoToOnlineDesigner = function() {
    if (pid > 0) window.location.href = app_path_webroot + "Design/online_designer.php?pid=" + pid;
};

SAN.GoToCustom = function(url) {
    window.location.href = url;
};


// add a new record to the current project
SAN.AddTestRecord = function() {
    if (pid > 0) {
        $.post({
            url: SAN.ajax_endpoint,
            dataType: 'json',
            data: { action: "getTestRecordId"},
        }).done( function(result) {
            // Result is an object with success and data
            if (result.success) {
                window.location.href = app_path_webroot + "DataEntry/record_home.php?pid="+pid+"&id="+result.data;
            } else {
                SAN.debug('unable to get test record', result);
            }
        }).always( function() {
            //dc.hideWaitingModal();
        });
    }
};



//////// SHOW HIDE CONSOLE AND MIRROR TEXTAREA /////////

// Mirror textarea contents to priorElement if enabled
SAN.textareaMirror = function() {
    if (SAN.textareaMirrorEnabled) {
        SAN.priorElement.val( SAN.textarea.val() );
    }
};


SAN.ShowConsole = function() {
    SAN.PrepareEditor();

    $('#shell-wrap').slideDown();

    // Set the tab to the editor
    if (SAN.textareaMirrorEnabled) {
        SAN.priorElement.prop("disabled",true);
        $('#san_editor_link').click();
        SAN.textarea.focus();
    } else {
        $('#san_search_link').click();
    }
};


SAN.HideConsole = function() {
    $('#shell-wrap').slideUp();

    // Re-enable the disabled element
    if (SAN.textareaMirrorEnabled) {
        SAN.priorElement.prop('disabled',false);
        SAN.textareaMirrorEnabled = false;
        SAN.textarea.val("");
    }

};


SAN.ToggleConsole = function() {
    // Hide if open
    if ($('#shell-wrap').is(":visible")) {
        SAN.HideConsole();
    } else {
        SAN.ShowConsole();
    }
};

// If the prior element is a textarea/input then default to the editor
SAN.PrepareEditor = function() {
    // Save source element that has focus
    SAN.priorElement = $(':focus');



    if (SAN.priorElement.is('textarea,input')
        && ( SAN.priorElement.prop('name') || SAN.priorElement.prop('id'))
        && ( SAN.priorElement.prop('type') !== 'button' )
    ) {
        // Set value from the prior element to our SAN textarea
        SAN.textarea.val( SAN.priorElement.val() );
        SAN.textareaMirrorEnabled = true;
    } else {
        SAN.priorElement = null;
        // SAN.textareaMirrorEnabled = false;
        // SAN.priorElement.prop("disabled", false);
    }
};














// SAN Logging Function

SAN.log = function() {
    if (!SAN.isDev) return false;

    // Make console logging more resilient to Redmond
    try {
        console.log.apply(this,arguments);
    } catch(err) {
        // Error trying to apply logs to console (problem with IE11)
        try {
            console.log(arguments);

        } catch (err2) {
            // Can't even do that!  Argh - no logging
            // var d = $('<div></div>').html(JSON.stringify(err)).appendTo($('body'));
        }
    }
};


SAN.handleEnter = function(e) {

    if (e.which === 13) {
        // enter pressed
        SAN.searchKeyWord = $(".shell-searchbox").val().trim();
        SAN.log(SAN.searchKeyWord);

        window.location.href = SAN.redcap_path_root+"ProjectSetup/index.php?pid=" + SAN.searchKeyWord;
        SAN.HideConsole();

    }
};





SAN.buttonPress = function() {
    var action = $(this).data('action');

    if (action === "blah") {
        SAN.log("Do Blah");
    }
    else {
        SAN.log(this, 'unconfigured buttonPress', action);
    }
};



// Get all the projects in the system
SAN.getAllProjects = function() {

    $.ajax({
        method: "POST",
        url: SAN.ajax_endpoint,
        dataType: 'json',
        data: { action: "get-all-projects"},
    }).done( function(result) {
        // Result is a single object - let's break it down into an array
        for (var key in result) {
            if (result.hasOwnProperty(key) && ! isNaN(key)) {
                var pid = key;
                var title = result[key];
                // console.log(pid + " -> " + title);
            }
        }
    }).always( function() {
        //dc.hideWaitingModal();
    });
};



SAN.addAlert = function (msg, alertType) {
    alertType = alertType || "alert-danger";

    $('<div id="update-alert" class="alert ' + alertType + ' alert-dismissible fade show">')
        .html(msg)
        .prepend('<button type="button" class="close" data-dismiss="alert">&times;</button>')
        .insertAfter('div.step1');
};

