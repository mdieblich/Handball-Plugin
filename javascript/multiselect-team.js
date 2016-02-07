jQuery(document).ready(function($) {
    $('#multiselect').multiselect({
        right: '#multi_d_to, #multi_d_to_2',
        rightSelected: '#multi_d_rightSelected, #multi_d_rightSelected_2',
        leftSelected: '#multi_d_leftSelected, #multi_d_leftSelected_2',
        rightAll: '#multi_d_rightAll, #multi_d_rightAll_2',
        leftAll: '#multi_d_leftAll, #multi_d_leftAll_2',

        moveToRight: function(Multiselect, options, event, silent, skipStack) {
            var button = $(event.currentTarget).attr('id');

            if (button == 'multi_d_rightSelected') {
                var left_options = Multiselect.left.find('option:selected');
                Multiselect.right.eq(0).append(left_options);

                for(i=0; i < options.length; i++){
                    addStammspieler(options[i].value);
                }

                if ( typeof Multiselect.callbacks.sort == 'function' && !silent ) {
                    Multiselect.right.eq(0).find('option').sort(Multiselect.callbacks.sort).appendTo(Multiselect.right.eq(0));
                }
            } else if (button == 'multi_d_rightSelected_2') {
                var left_options = Multiselect.left.find('option:selected');
                Multiselect.right.eq(1).append(left_options);

                for(i=0; i < options.length; i++){
                    addZusatzspieler(options[i].value);
                }

                if ( typeof Multiselect.callbacks.sort == 'function' && !silent ) {
                    Multiselect.right.eq(1).find('option').sort(Multiselect.callbacks.sort).appendTo(Multiselect.right.eq(1));
                }
            }
        },

        moveToLeft: function(Multiselect, options, event, silent, skipStack) {
            var button = $(event.currentTarget).attr('id');

            if (button == 'multi_d_leftSelected') {
                var right_options = Multiselect.right.eq(0).find('option:selected');
                Multiselect.left.append(right_options);

                for(i=0; i < options.length; i++){
                    removeStammspieler(options[i].value);
                }

                if ( typeof Multiselect.callbacks.sort == 'function' && !silent ) {
                    Multiselect.left.find('option').sort(Multiselect.callbacks.sort).appendTo(Multiselect.left);
                }
            } else if (button == 'multi_d_leftAll') {
                var right_options = Multiselect.right.eq(0).find('option');
                Multiselect.left.append(right_options);

                for(i=0; i < right_options.length; i++){
                    removeStammspieler(right_options[i].value);
                }

                if ( typeof Multiselect.callbacks.sort == 'function' && !silent ) {
                    Multiselect.left.find('option').sort(Multiselect.callbacks.sort).appendTo(Multiselect.left);
                }
            } else if (button == 'multi_d_leftSelected_2') {
                var right_options = Multiselect.right.eq(1).find('option:selected');
                Multiselect.left.append(right_options);

                for(i=0; i < options.length; i++){
                    removeZusatzspieler(options[i].value);
                }

                if ( typeof Multiselect.callbacks.sort == 'function' && !silent ) {
                    Multiselect.left.find('option').sort(Multiselect.callbacks.sort).appendTo(Multiselect.left);
                }
            } else if (button == 'multi_d_leftAll_2') {
                var right_options = Multiselect.right.eq(1).find('option');
                Multiselect.left.append(right_options);

                for(i=0; i < right_options.length; i++){
                    removeZusatzspieler(right_options[i].value);
                }

                if ( typeof Multiselect.callbacks.sort == 'function' && !silent ) {
                    Multiselect.left.find('option').sort(Multiselect.callbacks.sort).appendTo(Multiselect.left);
                }
            }
        }
    });
});
function addStammspieler(nutzerID){
    var data = {
            'action': 'add_stammspieler',
            'team': teamid,
            'user': nutzerID
    };
    jQuery.post(ajaxurl, data, function(response) {
        if(response != nutzerID){
            alert("Der Stammspieler konnte nicht hinzugefügt werden: " + response + " statt " + nutzerID);
        }
    });
}
function removeStammspieler(nutzerID){
    var data = {
            'action': 'remove_stammspieler',
            'team': teamid,
            'user': nutzerID
    };
    jQuery.post(ajaxurl, data, function(response) {
        if(response != nutzerID){
            alert("Der Stammspieler konnte nicht entfernt werden: " + response + " statt " + nutzerID);
        }
    });
}
function addZusatzspieler(nutzerID){
    var data = {
            'action': 'add_zusatzspieler',
            'team': teamid,
            'user': nutzerID
    };

    jQuery.post(ajaxurl, data, function(response) {
        if(response != nutzerID){
            alert("Der Stammspieler konnte nicht hinzugefügt werden: " + response + " statt " + nutzerID);
        }
    });
}
function removeZusatzspieler(nutzerID){
    var data = {
            'action': 'remove_zusatzspieler',
            'team': teamid,
            'user': nutzerID
    };

    jQuery.post(ajaxurl, data, function(response) {
        if(response != nutzerID){
            alert("Der Stammspieler konnte nicht entfernt werden: " + response + " statt " + nutzerID);
        }
    });
}