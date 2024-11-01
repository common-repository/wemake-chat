jQuery(function($){

    // Detect unsaved changes

    var wmch_ajax_queue = 0;

    $(window).bind("beforeunload", function(event){
        if(wmch_ajax_queue > 0) return wmch_language.unsaved_changes;
    });

    // Submit form

    $(document).on("submit", ".wmch-form", function(event){

        var $form = $(this),
            $result = $form.find(".wmch-result");

        // Show loading spiner

        $form.addClass("wmch-loading");

        // Hide message

        $result.removeClass("wmch-show wmch-error");

        // Set ajax queue

        wmch_ajax_queue++;

        // Ajax submit

        $form.ajaxSubmit({
            type: "POST",
            dataType: "json",
            success: function(data){

                var message = "";

                $result.addClass("wmch-show");

                // Success

                if(data.success){
                    message = wmch_language.success;
                }

                // Show errors

                if(typeof(data.error)!=="undefined" && data.error > 0){
                    alert(wmch_language["upload_error" + data.error]);
                }

                // Show thumbnail

                if(typeof(data.avatar)!=="undefined"){
                    $(".wmch-form-pic-img").html("<img src='" + data.avatar + "'>");
                }

                // Show message text

                $result.html(message);

                // Hide loading spiner

                $form.removeClass("wmch-loading");

                // Unset ajax queue

                wmch_ajax_queue--;

            }, error: function(){
                // Show alert
                alert(wmch_language.request_error);
                // Unset ajax queue
                wmch_ajax_queue--;
            }
        });

        event.preventDefault();

    });

    // Save last cursor position

    $(document).on("keyup click", ".wmch-allow-smiles input", function(){

        var $this = $(this), caret_pos = 0;

        if(document.selection){
            $this.trigger("focus");
            var sel = document.selection.createRange();
            sel.moveStart ('character', -$this[0].value.length);
            caret_pos = sel.text.length;
        }else if($this[0].selectionStart || $this[0].selectionStart==='0'){
            caret_pos = $this[0].selectionStart;
        }

        $this.attr("data-pos", caret_pos);

    });

    // Show or hide smiles

    $(document).on("click", ".wmch-show-smiles", function(){

        var $this = $(this), $popup = $(".wmch-smile-popup");

        $(".wmch-allow-smiles input.last").removeClass("last");
        $this.closest(".wmch-form-field").find("input").addClass("last");
        $popup.removeClass("show");

        window.setTimeout(function(){
            $popup.css("top", ($this.offset().top - $(window).scrollTop()) + "px");
            $popup.css("left", $this.offset().left + "px");
        },100);

        window.setTimeout(function(){
            $popup.addClass("show");
        },200);

    });

    $(window).on("scroll", function(){
        $(".wmch-smile-popup").removeClass("show");
    });

    $(document).on("click", ".wmch-smile-popup,.wmch-show-smiles", function(e){
        e.stopPropagation();
    });

    $(document).on("click", "body", function(){
        $(".wmch-smile-popup").removeClass("show");
    });

    // Insert smile

    $(document).on("click", ".wmch-smile-popup .smile", function(){

        var $this = $(this),
            $last = $(".wmch-allow-smiles input.last"),
            value = $last.val(),
            last_pos = $last.attr("data-pos");

        if(typeof(last_pos)==="undefined"){
            last_pos = 0;
        }

        $(".wmch-smile-popup").removeClass("show");

        var new_value = value.slice(0, last_pos) + $this.text() + value.slice(last_pos, value.length);

        $last.val(new_value);

    });

    // Checkboxes

    $(document).on("change", ".wmch-checkbox input[type='checkbox']", function(e){
        if($(this).prop("checked")){
            $(this).parents(".wmch-checkbox").addClass("checked");
        }else{
            $(this).parents(".wmch-checkbox").removeClass("checked");
        }
    });

    $(document).on("click", ".wmch-checkbox", function(e){

        e.preventDefault();
        e.stopPropagation();

        let $el = $(this).find("input[type='checkbox']");

        if($el.prop("checked")) $el.prop("checked", false).trigger("change");
        else $el.prop("checked", true).trigger("change");

    });

    $(".wmch-checkbox input[type='checkbox']").trigger("change");

    // Custom style for file input

    $.fn.initInputFileField = function(){
        $(this).simpleFileInput({
            placeholder : "",
            buttonText : $(this).attr("data-button-text")
        });
    };

    $(".custom-input-file").initInputFileField();

    var file_input_focus_tm = 0;

    $(document).on("click", ".wmch-form-pic-bt", function(){

        // Clear interval

        window.clearInterval(file_input_focus_tm);

        // Run SFI trigger

        $(".wmch-form-pic .sfi-trigger").trigger("click");

        // Detect changes

        file_input_focus_tm = window.setInterval(function(){

            let $sfi_filename = $(".wmch-form-pic .sfi-filename");

            if($sfi_filename.wmch_trim_text()){
                // Set file name
                $(".wmch-form-pic-sel").text($sfi_filename.text());
                // Clear interval
                window.clearInterval(file_input_focus_tm);
                // Add "selected" marker
                $(".wmch-form-pic").addClass("selected");
            }

        }, 500);

        return false;

    });

    // Remove avatar

    $(document).on("click", ".wmch-form-pic-rem", function(){
        var $sel = $(".wmch-form-pic-sel");
        $(".wmch-form input[name='remove_avatar']").val(1);
        $(".wmch-form input[name='popup_avatar']").val("");
        $(".wmch-form-pic").removeClass("selected");
        $(".wmch-form-pic img").remove();
        $sel.text($sel.attr("data-empty-text"));
    });

    // Validation

    $.fn.wmch_trim_text = function(){
        var s = $(this).text();
        if(!s){
            return false;
        }
        return s.replace(/ +/g, "");
    };

});