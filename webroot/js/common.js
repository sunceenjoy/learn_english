EngUtil = {
    showMessage: function (text, timeout, callback) {
        if (timeout) {
            this.messageTimeout = timeout;
        }

        var object = $('<div style="text-align: center; position: fixed; left: 0; right: 0; top:15%; margin:auto; height:50px; width:300px;" class="alert alert-success">' +
                        '<button type="button" class="close" data-dismiss="alert">x</button>' +
                        '<strong>' + text + '</strong>' +
                    '</div>');
        $(document.body).append(object);

        object.fadeTo(timeout * 1000, 500).slideUp(500, function(){
            object.remove();
            if (typeof callback != 'undefined') {
                callback();
            }
        });
    },
    showAlert: function (text) {
        var object = $('<div style="text-align: center; position: fixed; left: 0; right: 0; top:15%; margin:auto; height:50px; width:300px;" class="alert alert-danger">' +
                        '<button type="button" class="close" data-dismiss="alert">x</button>' +
                        '<strong>' + text + '</strong>' +
                    '</div>');
        $(document.body).append(object);
    },
    showSuccessToast: function (msg, closeFunction) {
        var close = closeFunction || null;
        $().toastmessage('showToast', {
            text     : msg,
            sticky   : false,
            position : 'top-center',
            type     : 'success', // notice, warning, error, success
            closeText: '',
            close    : close
        });
    },
    showErrorToast: function (msg, closeFunction) {
        var close = closeFunction || null;
        $().toastmessage('showToast', {
            text     : msg,
            sticky   : true,
            position : 'top-center',
            type     : 'error', // notice, warning, error, success
            closeText: '',
            close    : close
        });
    }
};