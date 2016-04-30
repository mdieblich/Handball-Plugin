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
function addStammspieler(user_id){
    var data = {
            'action': 'add_stammspieler',
            'team_id': team_id,
            'user_id': user_id
    };
    jQuery.post(ajaxurl, data, function(response) {
        if(response != user_id){
            alert("Der Stammspieler konnte nicht hinzugefügt werden: " + response + " statt " + user_id);
        }
    });
}
function removeStammspieler(user_id){
    var data = {
            'action': 'remove_stammspieler',
            'team_id': team_id,
            'user_id': user_id
    };
    jQuery.post(ajaxurl, data, function(response) {
        if(response != user_id){
            alert("Der Stammspieler konnte nicht entfernt werden: " + response + " statt " + user_id);
        }
    });
}
function addZusatzspieler(user_id){
    var data = {
            'action': 'add_zusatzspieler',
            'team_id': team_id,
            'user_id': user_id
    };

    jQuery.post(ajaxurl, data, function(response) {
        if(response != user_id){
            alert("Der Stammspieler konnte nicht hinzugefügt werden: " + response + " statt " + user_id);
        }
    });
}
function removeZusatzspieler(user_id){
    var data = {
            'action': 'remove_zusatzspieler',
            'team_id': team_id,
            'user_id': user_id
    };

    jQuery.post(ajaxurl, data, function(response) {
        if(response != user_id){
            alert("Der Stammspieler konnte nicht entfernt werden: " + response + " statt " + user_id);
        }
    });
}