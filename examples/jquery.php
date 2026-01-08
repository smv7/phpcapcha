<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>jQuery & jQuery UI Captcha</title>
    <!-- jQuery UI CSS -->
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <style>
        body { font-family: Arial, sans-serif; background: #333; color: #fff; padding: 50px; text-align: center; }
        #dialog-form { background: #444; border: 1px solid #555; }
        .ui-widget-content { background: #444; color: #eee; }
        .captcha-box { margin: 15px 0; text-align: center; background: #fff; padding: 10px; border-radius: 5px; }
        input.text { margin-bottom:12px; width:95%; padding: .4em; }
        fieldset { padding:0; border:0; margin-top:25px; }
    </style>
</head>
<body>

    <button id="open-captcha">Open Captcha Dialog</button>

    <div id="dialog-form" title="Verify User">
        <p class="validateTips">All form fields are required.</p>
        <form>
            <fieldset>
                <label for="name">Name</label>
                <input type="text" name="name" id="name" value="Jane Doe" class="text ui-widget-content ui-corner-all">
                
                <div class="captcha-box">
                    <img src="captcha-endpoint.php?type=numeric&length=5" id="jq-captcha" alt="captcha">
                    <button type="button" id="refresh-btn" class="ui-button ui-widget ui-corner-all ui-button-icon-only" title="Refresh">
                        <span class="ui-icon ui-icon-refresh"></span> Refresh
                    </button>
                </div>

                <label for="captcha">Enter Numbers</label>
                <input type="text" name="captcha" id="captcha-input" class="text ui-widget-content ui-corner-all">
         
                <!-- Allow form submission with keyboard without duplicating the dialog button -->
                <input type="submit" tabindex="-1" style="position:absolute; top:-1000px">
            </fieldset>
        </form>
    </div>
 
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
    <script>
    $( function() {
        var dialog, form,
        tips = $( ".validateTips" );
    
        function updateTips( t ) {
            tips.text( t ).addClass( "ui-state-highlight" );
            setTimeout(function() {
                tips.removeClass( "ui-state-highlight", 1500 );
            }, 500 );
        }
    
        function verifyCaptcha() {
            var input = $("#captcha-input").val();
            $.post("validate.php", { captcha: input }, function(data) {
                if(data.success) {
                    dialog.dialog( "close" );
                    alert("Success: " + data.message);
                } else {
                    $("#jq-captcha").attr("src", "captcha-endpoint.php?type=numeric&length=5&" + new Date().getTime());
                    $("#captcha-input").val("");
                    updateTips( data.message );
                }
            }, "json");
        }
    
        dialog = $( "#dialog-form" ).dialog({
            autoOpen: false,
            height: 450,
            width: 350,
            modal: true,
            buttons: {
                "Verify": verifyCaptcha,
                Cancel: function() {
                    dialog.dialog( "close" );
                }
            },
            close: function() {
                form[ 0 ].reset();
                tips.text( "All form fields are required." );
            }
        });
    
        form = dialog.find( "form" ).on( "submit", function( event ) {
            event.preventDefault();
            verifyCaptcha();
        });
    
        $( "#open-captcha" ).button().on( "click", function() {
            dialog.dialog( "open" );
            $("#jq-captcha").attr("src", "captcha-endpoint.php?type=numeric&length=5&" + new Date().getTime());
        });

        $("#refresh-btn").on("click", function() {
             $("#jq-captcha").attr("src", "captcha-endpoint.php?type=numeric&length=5&" + new Date().getTime());
        });
    });
    </script>
</body>
</html>
