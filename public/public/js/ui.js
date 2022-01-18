let QS_toast = {
    show: function (text, center, duration) {
        if ($('.QS-toast').length > 0) {
            $('.QS-toast').remove();
        }
        var toast = $("<div class='QS-toast'><span class='QS-toast-text'>" + text + "</span></div>");
        $(document.body).append(toast);
        toast.show();
        if (center) {
            toast.css({
                'top': '50%',
                'margin-top': -toast.outerHeight() + 'px'
            });
        } else {
            toast.css({
                'bottom': '3rem'
            });
        }
        toast.addClass('in');
        setTimeout(function () {
            toast.hide();
        }, duration || 1200);
    },
    hide: function () {
        $('.QS-toast').addClass('out').transitionEnd(function (e) {
            $('.QS-toast').remove();
        });
    }
};

function urlPost(url){
    return `${location.protocol}//${window.location.host}/index/${url}`
}

 //时间戳转时间
 function timeTransform(timestamp) {
    var date = new Date(timestamp * 1000);
    var Y = date.getFullYear() + '-';
    var M = (date.getMonth() + 1 < 10 ? '0' + (date.getMonth() + 1) : date.getMonth() + 1) + '-';
    var D = (date.getDate() < 10 ? '0' + date.getDate() : date.getDate()) + ' ';
    var h = (date.getHours() < 10 ? '0' + date.getHours() : date.getHours()) + ':';
    var m = (date.getMinutes() < 10 ? '0' + date.getMinutes() : date.getMinutes()) + ':';
    var s = (date.getSeconds() < 10 ? '0' + date.getSeconds() : date.getSeconds());
    return Y + M + D + h + m + s;
}